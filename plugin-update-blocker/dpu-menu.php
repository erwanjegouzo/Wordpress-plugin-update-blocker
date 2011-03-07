<?php

if(isset($_POST["action"]) && $_POST["action"] == "pub_save" && current_user_can('update_plugins')){
	$pub_plugins = array();
	
	foreach($_POST as $comp => $key){
		if($comp != "action"){
			if($key == 'disable-update'){
				$plugin_name = preg_replace("/_php/", ".php", htmlentities(strip_tags($comp)));
				$note = '';
				if(isset($_POST[$comp.'-note'])){$note = $_POST[$comp.'-note'];}
				$pub_plugins[$plugin_name] = array('name' => $plugin_name, 'note' => $note);
			}
		}
	}
	
	update_option(PUB_UPDATE_DEACTIVATED, serialize($pub_plugins));
	
	?>
    <div class='updated'>
    <p>Options saved!</p>
    </div>
    <?php
	set_site_transient('update_plugins', time()-864000);
	wp_update_plugins();
}
else{
	$pub_plugins = array();
	if(get_option(PUB_UPDATE_DEACTIVATED)){
		$pub_plugins = unserialize(get_option(PUB_UPDATE_DEACTIVATED));
	}
}

$wp_plugins = get_plugins();

?>
<style>
.form-table th {width:250px; padding-bottom:5px; padding-top:5px;}
.form-table td {padding-bottom:5px; padding-top:5px;}
span.dpu-note{ display:none;font-size:9px; color:#21759B; text-decoration:underline; cursor:pointer;}
textarea.dpu{ display:none; margin-bottom:15px; }
.form-table{ width:300px; }

tr.hover{ background:#fff;}

</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$("span.dpu-note").click(function(){
		rel= $(this).attr('rel');
		$("textarea[rel='"+rel+"']").toggle();
	});
	$("input[type='checkbox']").click(function(){
		rel= $(this).attr('rel');
		checked = $(this).attr('checked');
		if(checked){$("span[rel='"+rel+"']").show();}
		else{
			$("span[rel='"+rel+"']").hide();
			$("textarea[rel='"+rel+"']").hide();
		}
	});
	
	$("tr.dpu").hover(
		function(){ 
			$(this).addClass("hover");
		},
		function(){ 
			$(this).removeClass("hover"); 
		}
	);
	
});


</script>

<div class="wrap dpu-options">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php echo PUB_NAME; ?></h2>
    <p>
    Select for which plugins you want to disable updates.<br />You can then leave a note to explain the changes you made.<br />
    If a plugin you deactivated had a new version ready, you may need to refresh the page to hide it.
    </p>
	<form action="admin.php?page=<?php echo PUB_SLUG; ?>" method="post">
    <input type="hidden" name="action" value="pub_save">
    <table class="form-table">
        <tbody>
        <tr valign='top'>
            <th scope='row'><h3>Plugin name</h3></th>
            <td><h3>Deactivated</h3></td>
         </tr>
        <?php
        foreach($wp_plugins as $key => $wp_plugins){
			$cur_plugin = '';
			if(array_key_exists($key, $pub_plugins)){ 
				$cur_plugin = $pub_plugins[$key];
			}
		?>
        <tr valign='top' class='dpu'>
            <th scope='row'><?php  echo $wp_plugins["Name"]; ?><br /><span rel='<?php echo $wp_plugins["Name"]; ?>' class='dpu-note' <?php if($cur_plugin != ''){ echo ' style=\'display:inline-block;\''; } ?>>Add note</span><br /></th>
            <td align="right">
                <input type="checkbox" name="<?php echo $key; ?>" value="disable-update" rel='<?php echo $wp_plugins["Name"]; ?>' <?php if($cur_plugin != ''){ echo ' checked=\'checked\''; } ?> />
            </td>
         </tr>
         <tr>
         <td colspan="2" class='dpu-note'>
                <textarea cols='30' name="<?php echo $key; ?>-note" value="disable-update-note" class='dpu' rel='<?php echo $wp_plugins["Name"]; ?>' <?php if($cur_plugin != ''){ echo ' style=\'display:block;\''; } ?>><?php echo $cur_plugin['note'];?></textarea>
                </td>
         </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    <input type="submit" class='button-primary' value="deactivate updates for selected plugins">
    
    </form>
    
</div>
    