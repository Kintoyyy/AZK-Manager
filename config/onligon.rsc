### enable telegram notification, change from 0 to 1 if you want to enable telegram
:local enableTelegram 0;
###replace telegram token
:local telegramToken "xxxxxxxxx:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
###replace telegram chat id / group id
:local chatId "-xxxxxxxxx";
### enable JuanFi online monitoring 0 = DoNotSend,  1=send data to api
:local apiSend 0;
### derive from the JuanFi online monitoring, create account in genman.projectdorsu.com
:local URLvendoID 5; 
### enable Random MAC synchronizer
:local enableRandomMacSyncFix 1;
### hotspot folder for HEX put flash/hotspot for haplite put hotspot only
:local hotspotFolder "flash/hotspot";

:local com [/ip hotspot user get [find name=$user] comment];
/ip hotspot user set comment="" $user;

:if ($com!="") do={

	:local mac $"mac-address";
	:local macNoCol;
	:for i from=0 to=([:len $mac] - 1) do={ 
	  :local char [:pick $mac $i]
	  :if ($char = ":") do={
		:set $char ""
	  }
	  :set macNoCol ($macNoCol . $char)
	}
	
	:local validity [:pick $com 0 [:find $com ","]];
	
	:if ( $validity!="0m" ) do={
		:local sc [/sys scheduler find name=$user]; :if ($sc="") do={ :local a [/ip hotspot user get [find name=$user] limit-uptime]; :local c ($validity); :local date [ /system clock get date]; /sys sch add name="$user" disable=no start-date=$date interval=$c on-event="/ip hotspot user remove [find name=$user]; /ip hotspot active remove [find user=$user]; /ip hotspot cookie remove [find user=$user]; /system sche remove [find name=$user]; /file remove \"$hotspotFolder/data/$macNoCol.txt\";" policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive,romon; :delay 2s; } else={ :local sint [/sys scheduler get $user interval]; :if ( $validity!="" ) do={ /sys scheduler set $user interval ($sint+$validity); } };
	}
	
	:local infoArray [:toarray [:pick $com ([:find $com ","]+1) [:len $com]]];
	
	:local totaltime [/ip hotspot user get [find name="$user"] limit-uptime];
	:local amt [:pick $infoArray 0];
	:local ext [:pick $infoArray 1];
	:local vendo [:pick $infoArray 2];
	:local uactive [/ip hotspot active print count-only];

		    #api tracking

	    #BOF
	    { /do {    
	    :local URLamount "$amt";
	    :local URLcomment "ScriptOnLoginFINAL";
	    :local URLip [:put [:tostr $address]];
	    :local URLusr [$user];
	    :local URLmac [$"mac-address"];
	    :local URLipmac "$URLusr_$URLip_$URLmac";
	    :local URLactive [/ip hotspot active print count-only];

	    #fixed declaration 
	    :if ($apiSend!=0)  do={
	    /do {
	    :local fixUrl [("https://juanfiapi.projectdorsu.com/serve.js\?s=stats&i=OE-IBX-12345&m=direct&payload=$URLvendoID")];
	    :local apiUrl "$fixUrl_$URLamount_$URLipmac_$URLactive_$URLcomment";
	    :log debug "API SendInfo: $apiUrl ";
	    /tool fetch mode=https http-method=get url=$apiUrl keep-result=no
	    :delay 1s;
	    } on-error={:log error "API Vendo ERROR: $apiUrl ";} }
	    } on-error={:log error "APIvendoRoutineError";} }
	    #EOF

	    #end of api tracking
	
	:local getIncome [:put ([/system script get [find name=todayincome] source])];
	/system script set source="$getIncome" todayincome;

	:local getSales ($amt + $getIncome);
	/system script set source="$getSales" todayincome;

	:local getMonthlyIncome [:put ([/system script get [find name=monthlyincome] source])];
	/system script set source="$getMonthlyIncome" monthlyincome;

	:local getMonthlySales ($amt + $getMonthlyIncome);
	/system script set source="$getMonthlySales" monthlyincome;

	#Send Seller/Vendo Monthly sales

	:if ( [/system script find name=$vendo] != "" ) do={ 
			:local getVendo [/system script get [find name=$vendo] comment];
			:local vendorArray [:toarray [:pick $getVendo ([:find $getVendo ","]) [:len $getVendo]]];
			:local getMonthlySeller [:pick $vendorArray 0];
			:local getLastSales [:pick $vendorArray 1];
			:local addMonthly ($amt + $getMonthlySeller);
			:local getSellerIncome [:put ([/system script get [find name=$vendo] source])];
			:local getSellerSales ($amt + $getSellerIncome);
			/system script set source="$getSellerSales" comment="VendoSales,$addMonthly,$getLastSales" $vendo;
			:if ($enableTelegram=1) do={/tool fetch url="https://api.telegram.org/bot$telegramToken/sendmessage?chat_id=$chatId&text=<<======New Sales======>> %0A Seller: $vendo %0A Seller Sales : $getSellerSales %0A Seller Montly Sales : $addMonthly %0A Voucher: $user %0A IP: $address %0A MAC: $mac %0A Amount: $amt %0A Extended: $ext %0A Total Time: $totaltime %0A  %0A Today Sales : $getSales %0A Monthly Sales : $getMonthlySales %0A Active Users: $uactive%0A <<=====================>>" keep-result=no;};
		} else={ 
			:local comment "VendoSales,$amt,0";
			/system script add name=$vendo owner=admin comment=$comment source="$amt";
			/system scheduler add interval=4w3d name="Reset $vendo Income" on-event=":local getVendo [/system script get [find name=$vendo] comment];:local vendorArray [:toarray [:pick \$getVendo ([:find \$getVendoScript \",\"]) [:len \$getVendo]]];:local getLastSales [:pick \$vendorArray 1];/system script set [find name=$vendo] comment=\"VendoSales,0,\$getLastSales\";"  start-date=sep/01/2022 start-time=00:00:00;
	}

	:local validUntil [/system scheduler get $user next-run];

	/file print file="$hotspotFolder/data/$macNoCol" where name="dummyfile"; 
	:delay 1s; 
	/file set "$hotspotFolder/data/$macNoCol" contents="$user#$validUntil";
};

:if ($enableRandomMacSyncFix=1) do={
	:local cmac $"mac-address"
	:foreach AU in=[/ip hotspot active find user="$username"] do={
	  :local amac [/ip hotspot active get $AU mac-address];
	  :if ($cmac!=$amac) do={  /ip hotspot active remove [/ip hotspot active find mac-address="$amac"]; }
	}
}