<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$setting->site_name?></title>
<meta name='description' content='<?=$setting->meta_description?>' />
<meta name='keywords' content='<?=$setting->meta_tag?>' />
<style type='text/css'>
	body { background: url('<?=$conf->base?>/img/maintenance-bg.jpg') repeat-x; }
	p { text-align: center; position: absolute; top: 30%; left: 50%; margin-top: -140px; margin-left: -225px; background: url('<?=$conf->base?>/img/maintenance-msg.gif') no-repeat;  height: 282px; width: 451px; }
	span { color: white; font-size: 30px; font-weight: bold; font-family: Arial; padding-top: 20px; display: block;}
</style>
</head>

<body>
	<p>
		<span><?=$setting->site_name?></span>
	</p>
</body>
</html>