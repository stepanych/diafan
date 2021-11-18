<?php
/**
 * Файл-блок шаблона
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

if (! defined('DIAFAN'))
{
	$path = __FILE__;
	while(! file_exists($path.'/includes/404.php'))
	{
		$parent = dirname($path);
		if($parent == $path) exit;
		$path = $parent;
	}
	include $path.'/includes/404.php';
}
?>
<div id="top-line">
    <div class="wrapper">       
    <div class="slide_button"><i class="fa fa-navicon"></i></div>
      <div class="top-phone">      
        <insert name="show_theme" module="site" tag="phone">
      </div> 
      <div class="top-line-right">
			<!-- шаблонный тег вывода количества отложенных товаров. Вид формы редактируется в файле modules/wishlist/views/wishlist.view.show_block.php. -->
			<?php if($this->diafan->_site->theme('show_favorite'))
			{
				echo $this->diafan->_tpl->htmleditor('<insert name="show_block" module="wishlist">');
			}
			?>

      <!-- шаблонный тег вывода формы корзины. Вид формы редактируется в файле modules/cart/views/cart.view.show_block.php. -->
      <insert name="show_block" module="cart">
	  </div>

    </div>
  </div>
<div id="top-menuline">  
    <div class="wrapper">
      <div style="font-size: 20px; padding: 10px 27px; background-color: #eee;">
		<insert name="show_href" alt="title">
      </div>	         
    </div>
</div>  