<?php
/**
 * image_manager Addon
 *
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author markus.staab[at]redaxo[dot]de Markus Staab
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 *
 * @author jdlx / rexdev.de
 * @link https://github.com/jdlx/image_manager_ep
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 1.2.4
 */
?>
<h1>Funktionen:</h1>

<p>Addon zum generieren von Grafiken anhand von Bildtypen.</p>

<h1>Anwendung:</h1>
<ul>
<li>Die Bildtypen werden in der Verwaltung des Addons erstellt und konfiguriert.</li>
<li>Jeder Bildtyp kann beliebig viele Effekte enthalten, die auf das aktuelle Bild angewendet werden.</li>
<li>Zum einbinden eines Bildes muss dazu der Bildtyp in der Url notiert werden.</li>
</ul>

<h1>URL Beispiel:</h1>
<pre>
<code class="xform-form-code"><?php echo $REX["FRONTEND_FILE"]; ?>?rex_img_type=ImgTypeName&rex_img_file=ImageFileName</code>
</pre>
