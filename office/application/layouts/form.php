<?php echo $this->doctype(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php echo $this->headMeta(); ?>
    <?php /* TU PEUX CHANGER LE TITRE ETC, ENLEVE LA LIGNE CI DESSOUS ET MET <title>...*/ ?>
    <?php echo $this->headTitle(); ?>
    <?php echo $this->headLink()->prependStylesheet($this->baseUrl() . '/styles/default.css'); ?>
    <script type="text/javascript" src="<?php echo $this->baseUrl() . '/js/prototype.js'; ?>"></script>
    <script type="text/javascript" src="<?php echo $this->baseUrl() . '/js/lib/Catalogue/Dashboard.js'; ?>"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<?php include ('../../config.php'); ?>
<div id="main">
    <div id="header">
        <ul>
            <li><a href="http://<?php echo $baseURL ; ?>">Accueil</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>nous-connaitre.php">Nous connaître</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>travailler-avec-nous.php">Travailler avec nous</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>environnement.php">Environnement</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>produits/nos-produits.php">Produits</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>nos-references.php">Nos références</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>contact.php">Contact</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>newsletter.php">Newsletter</a></li>
            <li><a href="http://<?php echo $baseURL ; ?>technique.php">Technique</a></li>
            <li><a href="#">Devis</a></li>
        </ul>
        <form>
            <select name="worldwide" onchange="MM_jumpMenu('parent',this,0)">
                <option value="http://<?php echo $baseURL ; ?>">France</option>
                <option value="http://www.ecobeton.it">Italie</option>	
                <option value="http://www.ecobeton.com">Norvège</option>	
                <option value="http://www.ecobeton.de">Allemagne</option>	
                <option value="http://www.ecobeton.at">Australie</option>	
                <option value="http://www.ecobeton.es">Espagne</option>		
                <option value="http://www.porecobeton.pt">Portugal</option>	
                <option value="http://www.ecobeton.dk">Danmark</option>
            </select>
        </form>
        <div class="clear"></div>
    </div>
    <?php echo $this->layout()->content; ?>
    <?php include ('../../blocks/product.php'); ?>
    <?php include ('../../blocks/mea_01.php'); ?>
    <?php include ('../../blocks/footer.php'); ?>
</div>
<script type="text/javascript">
	<!--
	function MM_jumpMenu(targ,selObj,restore){ //v3.0
	  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	  if (restore) selObj.selectedIndex=0;
	}
	//-->
</script>
</body>
</html>
