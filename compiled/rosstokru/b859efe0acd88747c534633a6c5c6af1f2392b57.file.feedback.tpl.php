<?php /* Smarty version Smarty-3.1.18, created on 2015-04-06 20:36:22
         compiled from "/home/v/vdmgrup/rostokgroup.ru/public_html/design/rosstokru/html/feedback.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6431954045522c416361492-70465778%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b859efe0acd88747c534633a6c5c6af1f2392b57' => 
    array (
      0 => '/home/v/vdmgrup/rostokgroup.ru/public_html/design/rosstokru/html/feedback.tpl',
      1 => 1428321005,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6431954045522c416361492-70465778',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'message_sent' => 0,
    'name' => 0,
    'error' => 0,
    'email' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_5522c4164fc492_33390229',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5522c4164fc492_33390229')) {function content_5522c4164fc492_33390229($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include '/home/v/vdmgrup/rostokgroup.ru/public_html/Smarty/libs/plugins/function.math.php';
?>


<?php $_smarty_tpl->tpl_vars['canonical'] = new Smarty_variable("/".((string)$_smarty_tpl->tpl_vars['page']->value->url), null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['canonical'] = clone $_smarty_tpl->tpl_vars['canonical'];?>

<h1><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['page']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</h1>

<?php echo $_smarty_tpl->tpl_vars['page']->value->body;?>


<h2>Обратная связь</h2>

<?php if ($_smarty_tpl->tpl_vars['message_sent']->value) {?>
	<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>
, ваше сообщение отправлено.
<?php } else { ?>
<form class="form feedback_form" method="post">
	<?php if ($_smarty_tpl->tpl_vars['error']->value) {?>
	<div class="message_error">
		<?php if ($_smarty_tpl->tpl_vars['error']->value=='captcha') {?>
		Неверно введена капча
		<?php } elseif ($_smarty_tpl->tpl_vars['error']->value=='empty_name') {?>
		Введите имя
		<?php } elseif ($_smarty_tpl->tpl_vars['error']->value=='empty_email') {?>
		Введите email
		<?php } elseif ($_smarty_tpl->tpl_vars['error']->value=='empty_text') {?>
		Введите сообщение
		<?php }?>
	</div>
	<?php }?>
	<label>Имя</label>
	<input data-format=".+" data-notice="Введите имя" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>
" name="name" maxlength="255" type="text"/>
 
	<label>Email</label>
	<input data-format="email" data-notice="Введите email" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['email']->value, ENT_QUOTES, 'UTF-8', true);?>
" name="email" maxlength="255" type="text"/>
	
	<label>Сообщение</label>
	<textarea data-format=".+" data-notice="Введите сообщение" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value, ENT_QUOTES, 'UTF-8', true);?>
" name="message"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value, ENT_QUOTES, 'UTF-8', true);?>
</textarea>

	<input class="button" type="submit" name="feedback" value="Отправить" />

	<div class="captcha"><img src="captcha/image.php?<?php echo smarty_function_math(array('equation'=>'rand(10,10000)'),$_smarty_tpl);?>
"/></div> 
	<input class="input_captcha" id="comment_captcha" type="text" name="captcha_code" value="" data-format="\d\d\d\d" data-notice="Введите капчу"/>
	
</form>
<?php }?><?php }} ?>
