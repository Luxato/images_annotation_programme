<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/flatly/bootstrap.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">VGIS8 - Annotated images</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01"
            aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a href="">Go Annotating!</a>
    <div class="collapse navbar-collapse" id="navbarColor01">
    </div>
</nav>

<?php
function compare_times( $a, $b ) {
    return $a['last_modified'] < $b['last_modified'] ? 1 : - 1;
}

$data = scandir( './data/annotations/' );
unset( $data[0] );
unset( $data[1] );
$images = [];
foreach ( $data as $image ) {
    $images[] = [
        'image'         => $image,
        'last_modified' => filectime( './data/annotations/' . $image )
    ];
}
usort( $images, 'compare_times' );
?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
        <h2>Annotated images</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Image</td>
                    <td>Last modified</td>
                    <td>Reviewed</td>
                </tr>
                </thead>
                <tbody>
                <?php $i = sizeof( $images );
                foreach ( $images as $image ): ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><a target="_blank"
                               href="http://claasdata.stranovsky.cloud/?image=<?= str_replace( '.xml', '.bmp', $image['image'] ) ?>"><?= str_replace( '.xml', '.bmp', $image['image'] ) ?></a>
                        </td>
                        <td><?= date( 'd.m.Y H:i', $image['last_modified'] + 2 * 3600 ) ?></td>
                        <td>TBA</td>
                    </tr>
                    <?php $i --; ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h2>Ignored images</h2>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td>#</td>
                    <td>Image</td>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $handle = fopen("./data/ignored.txt", "r");
                        $i = 0;
                        while (($line = fgets($handle)) !== false) {
                            $i++;
                            echo "<tr>";
                            echo "<td>$i</td>";
                            ?>

                            <td><a target="_blank"
                                   href="http://claasdata.stranovsky.cloud/?image=<?= str_replace( '.xml', '.bmp', $line ) ?>"><?= str_replace( '.xml', '.bmp', $line ) ?></a></td>

                            <?php
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

<?php


/*echo "<pre>";
print_r($data);
echo "</pre>";*/

?>