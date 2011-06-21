#!/usr/bin/php5
<?php
chdir('en');

shell_exec('xsltproc --xinclude --output ../html/en/index.html ../xsl/website/docbook.xsl Documentation.xml');
