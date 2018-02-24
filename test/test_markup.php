<?php

namespace Vatrikovsky\Core;
require_once __DIR__ . '/../src/Markup.php';


if ( isset( $_POST['text'] ) and $_POST['text'] )
	$text = $_POST['text'];
else
	$text =
		'  Это обычный текстовый текст. Нет, //тестовый текст//. Да, **тестовый тест!**' . "\r\n" .
		'image.png    ' . "\r\n" .
		'Caption for Image' . "\r\n" .
		"\r\n" .
		'((http://ya.ru)) ((Just letters))' . "\r\n" .
		"\r\n" .
		"\r\n" .
		' https://www.youtube.com/watch?v=Ep6SQcMg3Jk' . "\r\n" .
		'Video Caption' . "\r\n" .
		"\r\n";
?>

<!DOCTYPE HTML>
<html>
	<head>
    	<meta charset="utf-8" />
        <title>Vatrikovsky — Markup Test</title>
    </head>
    <body>
    	<form method="post">
        	<textarea name="text" rows=10 cols=120><?= $text ?></textarea><br />
            <button type="submit">Submit</button>
        </form>
        <main>
			<?php
                Markup::disableAll();	
                Markup::enableMethod( 'strong' );
                Markup::enableMethod( 'img' );			
                Markup::enableAll();			
                $text = Markup::all( $text );
                echo $text;
            ?>
        </main>
	</body>
</html>