<?php
if ( !isset( $_SESSION[ 'loggedin' ] ) ) {
  header( 'location: logout.php' );
  exit;
}
error_reporting( 0 );
ini_set( 'display_errors', 0 );
$clock   = $API->comm( "/system/clock/print" );
$sellers = $API->comm( "/system/script/print" );
// Remaining vouchers
$hsAllUsers  = $API->comm( "/ip/hotspot/user/print" );
$remVouchers = array();
foreach ( $hsAllUsers as $codes ) {
    if ( isset( explode( ",", $codes[ 'comment' ] )[ 3 ] ) ) {
        $remVouchers[] = array( 'name' => explode( ",", $codes[ 'comment' ] )[ 3 ], 'type' => explode( ",", $codes[ 'comment' ] )[ 1 ], 'disabled' => $codes[ 'disabled' ], 'id' => $codes[ '.id' ] );
        $typVouchers[] = array( 'type' => explode( ",", $codes[ 'comment' ] )[ 1 ] );
    }
}
$types = array_unique( $typVouchers, SORT_REGULAR );

$sharee = $MikroTik[ 'share' ];

?>

<?php $Totalsellers = count( $sellers );
for ( $i = 0; $i < $Totalsellers; $i++ ) {
    if ( isset( $sellers[ $i ][ 'comment' ] ) && explode( ",", ( $sellers[ $i ][ 'comment' ] ) )[ 0 ] == 'VendoSales' && $sellers[ $i ][ 'name' ] == $_SESSION[ 'username' ] ) {
        $last  = explode( "/", explode( " ", $sellers[ $i ][ 'last-started' ] )[ 0 ] )[ 0 ] . '-' . explode( "/", explode( " ", $sellers[ $i ][ 'last-started' ] )[ 0 ] )[ 1 ];
        $today = explode( "/", $clock[ 0 ][ 'date' ] )[ 0 ] . '-' . explode( "/", $clock[ 0 ][ 'date' ] )[ 1 ];
        ?>
        <div class="container py-3">
            <h2 class="text-center">Welcome <Span>
                    <?= $sellers[ $i ][ 'name' ] ?>
                </Span>!</h2>
            <div class="row mt-3">
                <div class="col-xl mb-2">
                    <div class="card bg-light text-center">
                        <div class="card-header">
                            <h2>Sales</h2>
                        </div>
                        <div class="row card-body">
                            <h3>Total Sales: <span class="badge rounded-pill bg-primary">
                                    <?= $MikroTik[ 'currency' ] . number_format( $sellers[ $i ][ 'source' ] ) ?>
                                </span></h3>
                            <p><span class="badge bg-warning text-dark sm">
                                    <?= $last . ' to ' . $today ?>
                                </span></p>
                            <div class="col">
                                <div class="col row">
                                    <div class="col">
                                        <h2 class="col">Your Profit</h2>
                                        <h1 class="card-title"><span class="badge rounded-pill bg-success">
                                                <?= $MikroTik[ 'currency' ] . number_format( $sellers[ $i ][ 'source' ] * ( 100 - $sharee ) * .01 ) ?>
                                            </span></h3>
                                            <span class="badge bg-warning text-dark sm">
                                                <?= ( 100 - $sharee ) . "% of " . $MikroTik[ 'currency' ] . number_format( $sellers[ $i ][ 'source' ] ) ?>
                                            </span>
                                    </div>
                                    <div class="col">
                                        <h2 class="col">Last Profit</h2>
                                        <h1 class="card-title"><span class="badge rounded-pill bg-success">
                                                <?= $MikroTik[ 'currency' ] . number_format( explode( ",", ( $sellers[ $i ][ 'comment' ] ) )[ 2 ] * ( 100 - $sharee ) * .01 ) ?>
                                            </span></h1>
                                        <span class="badge bg-warning text-dark sm">
                                            <?= isset( $sellers[ $i ][ 'last-started' ] ) ? ( explode( " ", $sellers[ $i ][ 'last-started' ] ) )[ 0 ] : '' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion accordion-flush"
                            id="accordionFlushExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapseTwo"
                                        aria-expanded="false"
                                        aria-controls="flush-collapseTwo">
                                        Earnings
                                    </button>
                                </h2>
                                <div id="flush-collapseTwo"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body row">
                                        <div class="col">
                                            <h2 class="col">This Month</h2>
                                            <h1 class="card-title"><span class="badge bg-success">
                                                    <?= $MikroTik[ 'currency' ] . number_format( explode( ",", ( $sellers[ $i ][ 'comment' ] ) )[ 1 ] ) ?>
                                                </span></h3>
                                        </div>
                                        <div class="col">
                                            <h2 class="col">Today</h2>
                                            <h1 class="card-title"><span class="badge bg-success">
                                                    <?= $MikroTik[ 'currency' ] . number_format( explode( ",", ( $sellers[ $i ][ 'comment' ] ) )[ 3 ] ) ?>
                                                </span></h1>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col mb-2">
                    <div class="card bg-light text-center">
                        <h2 class="card-header">
                            Remaining Vouchers
                        </h2>
                        <div class="card-body">
                            <div class="col">
                                <div class="row pb-2">
                                    <?php

                                    $count = 0;
                                    foreach ( $remVouchers as $value ) {
                                        if ( $value[ 'name' ] == $sellers[ $i ][ 'name' ] && $value[ 'disabled' ] == "false" ) {
                                            $count++;
                                        }
                                    }


                                    if ( $count != 0 ) {
                                        echo '<div class="col"><h3>Total: <span class="badge bg-primary rounded-pill">' . number_format( $count ) . ' pcs</span></h3>';
                                    }
                                    else {
                                        echo '<h2 class="badge mx-1 fw-bold bg-danger">No Vouchers!</h2> </div>';
                                    }

                                    $tag = 0;
                                    foreach ( $remVouchers as $value ) {
                                        if ( $value[ 'name' ] == $sellers[ $i ][ 'name' ] && $value[ 'disabled' ] == "true" ) {
                                            $tag++;
                                        }
                                    }
                                    if ( $tag != 0 ) {
                                        echo '<div class="col"><h1 class="badge bg-warning text-dark">Pending Vouchers - ' . number_format( $tag ) . ' pcs</h4></div>';
                                    }


                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <?php foreach ( $types as $t ) {
                                    $c = 0;
                                    foreach ( $remVouchers as $value ) {
                                        if ( $value[ 'name' ] == $sellers[ $i ][ 'name' ] && $value[ 'type' ] == $t[ 'type' ] && $value[ 'disabled' ] == "false" ) {
                                            $c++;
                                        }
                                    }
                                    if ( $c != 0 ) {
                                        if ( $c < 25 ) {
                                            echo '<div class="col">
                                                <h3><span class="badge rounded-pill bg-danger">' . $MikroTik[ 'currency' ] . $t[ 'type' ] . ' - ' . $c . ' pcs. left</span></h3>
                                            </div>';
                                        }
                                        else {
                                            echo '<div class="col">
                                                <h3><span class="badge rounded-pill bg-success">' . $MikroTik[ 'currency' ] . $t[ 'type' ] . ' - ' . $c . ' pcs. left</span></h3>
                                            </div>';
                                        }
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex gap-2 mt-2">
                            <a class="col btn btn-warning"
                                href="index.php?page=qr_scan">Scan Qr Code <i class="fas fa-qrcode"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- <div class="row mt-3">
            <div class="col-xl mb-2">
                <div class="card bg-light text-center">
                    <h5 class="card-header">
                        Contact Admin
                        </h3>
                        <div class="card-body">
                            <a href="https://m.me/kint.oyyy508" class="btn btn-primary">Messenger</a>
                        </div>
                </div>
            </div>
        </div> -->
        <?php
    }
}
; ?>