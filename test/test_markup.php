<?php

namespace Vatrikovsky\Core;
require_once __DIR__ . '/../src/Markup.php';


if ( isset( $_POST['text'] ) and $_POST['text'] )
	$text = $_POST['text'];
else
	$text =
		'  Это обычный текстовый текст. Нет, //тестовый текст//. Да, **тестовый тест!** Пусть он будет хотя бы на две строки, а то так неинтересно даже.' . "\r\n" .
		'https://www.film.ru/sites/default/files/people/1546123-881008.jpg    ' . "\r\n" .
		'Caption for Image' . "\r\n" .
		'((http://ya.ru)) ((Just letters)) //((https://ya.ru Это «Яндекс», детка!))//' . "\r\n" .
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
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i&amp;subset=cyrillic,cyrillic-ext,latin-ext" />
        <style>
			* { margin:0; padding:0; border-collapse:collapse; }
			body { padding: 32px 24px; font: 400 18px/24px 'Roboto', sans-serif;; }
			b, strong { font-weight:500; }
			main { max-width:1200px; }
			textarea { width:720px; height:240px; }
			form { margin-bottom:1.6em; }
			a { text-decoration:none; color:rgb(51,102,255); border-bottom:1px solid rgba(51,102,255,.3); }
			a:hover { color:rgb(240,0,0); border-color:rgba(240,0,0,.3); }
			p.v-text { max-width:720px; padding:8px 0; }
			p.v-caption { font-size:14px; line-height:18px; }
			div.v-image img { max-width:100%; }
			span.v-hpspace-quote { margin-right: 0.42em; }
			span.v-hpquote { margin-left: -0.42em; }
			span.v-hpspace-bracket { margin-right: 0.3em; }
			span.v-hpbracket { margin-left: -0.3em; }
        </style>
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