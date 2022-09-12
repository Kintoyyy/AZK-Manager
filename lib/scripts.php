
<h4>Onlogin Script</h4>
<pre class="border">
### enable telegram notification, change from 0 to 1 if you want to enable telegram
:local enableTelegram 1;
###replace telegram token
:local telegramToken "cvwjehvl7&094123ohjBJH47127LKJBL";
###replace telegram chat id / group id
:local chatId "981239121";
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
	
	:local getIncome [:put ([/system script get [find name=todayincome] source])];
	/system script set source="$getIncome" todayincome;

	:local getSales ($amt + $getIncome);
	/system script set source="$getSales" todayincome;

	:local getMonthlyIncome [:put ([/system script get [find name=monthlyincome] source])];
	/system script set source="$getMonthlyIncome" monthlyincome;

	:local getMonthlySales ($amt + $getMonthlyIncome);
	/system script set source="$getMonthlySales" monthlyincome;

	:if ( [/system script find name=$vendo] != "" ) do={ 
			:local getVendorScript [/system script get [find name=$vendo] comment];:local vendorArray [:toarray [:pick $getVendorScript ([:find $getVendorScript ","]) [:len $getVendorScript]]];:local getMontlhySales [:pick $vendorArray 0];:local getLastSales [:pick $vendorArray 1];:local addMonthly ($amt + $getMontlhySales);:local getSellerIncome [:put ([/system script get [find name=$vendo] source])];:local getSellerSales ($amt + $getSellerIncome);/system script set source="$getSellerSales" comment="VendoSales,$addMonthly,$getLastSales" $vendo;
			:if ($enableTelegram=1) do={/tool fetch url="https://api.telegram.org/bot$telegramToken/sendmessage?chat_id=$chatId&text=<<======New Sales======>> %0A Seller: $vendo %0A Seller Sales : $getSellerSales %0A Seller Montly Sales : $addMonthly %0A Voucher: $user %0A IP: $address %0A MAC: $mac %0A Amount: $amt %0A Extended: $ext %0A Total Time: $totaltime %0A  %0A Today Sales : $getSales %0A Monthly Sales : $getMonthlySales %0A Active Users: $uactive%0A <<=====================>>" keep-result=no;};
		} else={ 
			:local comment "VendoSales,$amt,0";/system script add name=$vendo owner=admin comment=$comment source="$amt";/system scheduler add interval=4w2d name="Reset $vendo Income" on-event=":local getVendorScript [/system script get [find name=$vendo] comment];:local vendorArray [:toarray [:pick \$getVendorScript ([:find \$getVendoScript \",\"]) [:len \$getVendorScript]]];:local getLastSales [:pick \$vendorArray 1];/system script set [find name=$vendo] comment=\"VendoSales,0,\$getLastSales\";"  start-date=sep/01/2022 start-time=00:00:00;
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
</pre>
<hr>
<h4>Onlogout Script</h4>
<pre class="border">
### hotspot folder for HEX put flash/hotspot for haplite put hotspot only
:local hotspotFolder "flash/hotspot";

:local mac $"mac-address";
:local macNoCol;
:for i from=0 to=([:len $mac] - 1) do={ 
  :local char [:pick $mac $i]
  :if ($char = ":") do={
	:set $char ""
  }
  :set macNoCol ($macNoCol . $char)
}
	
:if ([/ip hotspot user get [/ip hotspot user find where name="$user"] limit-uptime] <= [/ip hotspot user get [/ip hotspot user find where name="$user"] uptime]) do={
    /ip hotspot user remove $user;
	/file remove "$hotspotFolder/data/$macNoCol.txt";
	/system sche remove [find name=$user];
}
</pre>
<hr>
<h6>Onetime only Setup script (paste in Terminal)</h6>
<p>Credits to <a class="text-decoration-none" href="https://github.com/ivanalayan15/JuanFi">JuanFi System</a></p>
<pre class="border">
/system scheduler add interval=1d name="Reset Daily Income" on-event="/system script set source=\"0\" todayincome " policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive,romon start-date=Sep/28/2021 start-time=00:00:00;
/system scheduler add interval=30d name="Reset Monthly Income" on-event="/system script set source=\"0\" monthlyincome " policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive,romon start-date=Sep/28/2021 start-time=00:00:00;
/system script add dont-require-permissions=no name=todayincome owner=admin policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive,romon source="0";
/system script add dont-require-permissions=no name= monthlyincome owner=admin policy=ftp,reboot,read,write,policy,test,password,sniff,sensitive,romon source="0";
</pre>
<hr>