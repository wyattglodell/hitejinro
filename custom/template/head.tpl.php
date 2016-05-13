<base href='<?php echo $https?>' />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $metatitle?></title>
<meta name='description' content='<?php echo $metadescription?>' />
<meta name='keywords' content='<?php echo $metakeyword?>' />
<link rel="shortcut icon" href="<?=$favicon?>" type="image/x-icon"> 

<script>
  (function(d) {
    var config = {
      kitId: "iry6rtv",
      scriptTimeout: 3000,
      async: true
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src="https://use.typekit.net/"+config.kitId+".js";tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>

<?php echo $head_inc ?>

<script type='text/javascript'>
	var config = <?php echo $js_vars ?>;
</script>

<?php echo $css_inc ?>
<?php echo $js_inc ?>