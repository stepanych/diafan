<?php
/**
 * Шаблон страницы товара
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2020 OOO «Диафан» (http://www.diafan.ru/)
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



echo '<section class="section-d section-d_id section-d_shop section-d_shop_id">';
echo '<section class="product-d js_shop_id js_shop">';

echo
'<div class="product-d__images">';

	//вывод изображений товара
	if(! empty($result["img"]))
	{
		echo '<div class="product-d__gall">';

		echo
		'<div class="product-d__present gall-d swiper-container" data-gall-thumbs=".product-d__preview">
			<div class="gall-d__list swiper-wrapper">';
				$k = 0;
				foreach($result["img"] as $img)
				{
					echo '<div class="slide-d slide-d_fit swiper-slide" data-gall-slide-index="'.$k.'">';
					switch ($img["type"])
					{
						case 'animation':
							echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'shop" image_id="'.$img["id"].'">';
							break;
						case 'large_image':
							echo '<a href="'.BASE_PATH.$img["link"].'" rel="large_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'" image_id="'.$img["id"].'">';
							break;
						default:
							echo '<a href="'.BASE_PATH.$img["link"].'" image_id="'.$img["id"].'">';
							break;
					}
					echo '<img class="shop_id_img" src="'.BASE_PATH.$img["link"].'" alt="'.$img["alt"].'" title="'.$img["title"].'" image_id="'.$img["id"].'">';
					echo '</a>';
					echo '</div>';
					$k++;
				}
				echo
			'</div>';
			echo
			'<div class="gall-d__nav">
				<button class="gall-d__button gall-d__button_prev swiper-button-prev" title="'.$this->diafan->_('Предыдущий', false).'" type="button">
					<span class="icon-d fas fa-chevron-circle-left"></span>
				</button>
				<button class="gall-d__button gall-d__button_next swiper-button-next" title="'.$this->diafan->_('Следующий', false).'" type="button">
					<span class="icon-d fas fa-chevron-circle-right"></span>
				</button>
			</div>';
			echo
		'</div>';

		if($result["preview_images"])
		{
			echo
			'<div class="product-d__preview gall-d swiper-container" data-gall-show="4" data-gall-gap="10" data-gall-simulate-touch="true">
				<div class="gall-d__list swiper-wrapper">';
					$k = 0;
					foreach($result["img"] as $img)
					{
						echo
						'<div class="slide-d slide-d_fit swiper-slide _bordered" data-gall-slide-index="'.$k.'">
							<img src="'.$img["preview"].'" alt="" image_id="'.$img["id"].'">
						</div>';
						$k++;
					}
					echo
				'</div>';
				echo
				'<div class="gall-d__nav">
					<button class="gall-d__button gall-d__button_prev swiper-button-prev" title="'.$this->diafan->_('Предыдущий', false).'" type="button">
						<span class="icon-d fas fa-chevron-circle-left"></span>
					</button>
					<button class="gall-d__button gall-d__button_next swiper-button-next" title="'.$this->diafan->_('Следующий', false).'" type="button">
						<span class="icon-d fas fa-chevron-circle-right"></span>
					</button>
				</div>';
				echo
			'</div>';
		}

		echo '</div>';
	}
	else
	{
		echo '<figure class="_dummyimage"></figure>';
	}

	echo '<div class="product-d__stickers stickers-d">';
	if(! empty($result['hit']))
	{
		echo '<div class="sticker-d sticker-d_hit">'.$this->diafan->_('Хит').'</div>';
	}
	if(! empty($result['action']))
	{
		echo '<div class="sticker-d sticker-d_action">'.$this->diafan->_('Акция').'</div>';
	}
	if(! empty($result['new']))
	{
		echo '<div class="sticker-d sticker-d_new">'.$this->diafan->_('Новинка').'</div>';
	}
	echo '</div>';

	echo
	'<div class="product-d__interacts interacts-d">';
        if(empty($result["access_buy"]))
        {
            echo '
    		<button class="interact-d interact-d_wish'.(! empty($result["wish"]) ? ' _active' : '').' js_shop_wishlist" type="button"
    			title="'.(! empty($result['wish']) ? $this->diafan->_('Убрать из избранного', false) : $this->diafan->_('Добавить в избранное', false)).'"
    			data-title1="'.$this->diafan->_('Добавить в избранное', false).'"
    			data-title2="'.$this->diafan->_('Убрать из избранного', false).'">
    			<span class="interact-d__icon icon-d far fa-heart"></span>
    		</button>';
        }
		if(empty($result["hide_compare"]))
		{
			echo $this->get('compare_form', 'shop', $result);
		}
		echo
	'</div>';

	echo
'</div>';

echo
'<div class="product-d__details details-d">';

	echo
	'<div class="detail-d detail-d_params">';

		//вывод производителя
		if (! empty($result["brand"]))
		{
			echo
			'<div class="param-d param-d_brand">
				<span class="param-d__name">'.$this->diafan->_('Производитель').':</span>
				<strong class="param-d__value">
					<a href="'.BASE_PATH_HREF.$result["brand"]["link"].'">'.$result["brand"]["name"].'</a>
				</strong>';
				if (! empty($result["brand"]["img"]))
				{
					echo '<div class="param-d__images _images">';
					foreach ($result["brand"]["img"] as $img)
					{
						switch ($img["type"])
						{
							case 'animation':
								echo '<a href="'.BASE_PATH.$img["link"].'" data-fancybox="gallery'.$result["id"].'shop_brand">';
								break;
							case 'large_image':
								echo '<a href="'.BASE_PATH.$img["link"].'" rel="large_image" width="'.$img["link_width"].'" height="'.$img["link_height"].'">';
								break;
							default:
								echo '<a href="'.BASE_PATH_HREF.$img["link"].'">';
								break;
						}
						if($img["source"])
						{
							echo $img["source"];
						}
						else
						{
							echo '<img src="'.$img["src"].'" width="'.$img["width"].'" height="'.$img["height"].'" alt="'.$img["alt"].'" title="'.$img["title"].'">';
						}
						echo '</a>';
					}
					echo '</div>';
				}
				echo
			'</div>';
		}

		//вывод артикула
		if(! empty($result["article"]))
		{
			echo
			'<div class="param-d param-d_code">
				<span class="param-d__name">'.$this->diafan->_('Артикул').': </span>
				<strong class="param-d__value">'.$result["article"].'</strong>
			</div>';
		}

		echo
	'</div>';

	//вывод рейтинга товара
	if(! empty($result["rating"]))
	{
		echo '<div class="detail-d detail-d_rating">'.$result["rating"].'</div>';
	}

	//вывод наличия на складе
	// echo
	// '<div class="detail-d detail-d_stores">
	// 	<div class="detail-d__name">'.$this->diafan->_('Наличие').':</div>
	// 	<div class="detail-d__content">
	// 		<div class="store-d store-d_many">
	// 			<span class="store-d__name">Печатники</span>
	// 			<span> - </span>
	// 			<span class="store-d__count">15 </span>
	// 			<span class="store-d__unit">шт.</span>
	// 		</div>
	// 		<div class="store-d store-d_few">
	// 			<span class="store-d__name">Котельники</span>
	// 			<span> - </span>
	// 			<span class="store-d__count">5 </span>
	// 			<span class="store-d__unit">шт.</span>
	// 		</div>
	// 	</div>
	// </div>';

	//кнопка "Купить"
	echo $this->get('buy_form', 'shop', array("row" => $result, "result" => $result));

	echo '<div class="detail-d detail-d_socnets">';
	echo $this->htmleditor('<insert name="show_social_links">');
	echo '</div>';

	//счетчик просмотров
	if(! empty($result["counter"]))
	{
		echo
		'<div class="detail-d detail-d_counter counter-d">
			<div class="detail-d__name counter-d__name">'.$this->diafan->_('Просмотров').':</div>
			<div class="detail-d__content counter-d__valie">'.$result["counter"].'</div>
		</div>';
	}

	//теги товара
	if(! empty($result["tags"]))
	{
		echo '<div class="detail-d detail-d_tags">';
		echo $result["tags"];
		echo '</div>';
	}

	echo
'</div>';

echo '<div class="product-d__attributes _bordered">';
echo $this->htmleditor('<insert name="show_theme" module="site" tag="delivery" defer="emergence">');
echo '</div>';

echo
'<div class="product-d__tabs tabs-d tabs-d_sm tabs-d_gray tabs-d_sm_gray">';
	echo
	'<div class="tabs-d__tabnames">
		<a class="tabname-d tabname-d_active">
			<span class="tabname-d__icon icon-d fas fa-file-alt"></span>
			<span class="tabname-d__name">'.$this->diafan->_('Описание').'</span>
		</a>
		<a class="tabname-d">
			<span class="tabname-d__icon icon-d fas fa-list-alt"></span>
			<span class="tabname-d__name">'.$this->diafan->_('Характеристики').'</span>
		</a>
		<a class="tabname-d">
			<span class="tabname-d__icon icon-d far fa-comments"></span>
			<span class="tabname-d__name">'.$this->diafan->_('Комментарии').'</span>';
			if(!empty($result['comments_count']))
			{
				echo
				'<span class="tabname-d__amount amount-d">
					<strong class="amount-d__num">'.$result['comments_count'].'</strong>
				</span>';
			}
			echo
		'</a>
	</div>';
	echo
	'<div class="tabs-d__stack">
		<div class="product-d__tab product-d__tab_text tab-d tab-d_active _text">';
			if(! empty($result["good_set"]))
			{
				echo $this->htmleditor('<insert name="show_block_set" module="shop" count="20" images="1">');
			}
			//полное описание товара
			echo $this->htmleditor($result['text']);
			echo
		'</div>
		<div class="product-d__tab product-d__tab_params tab-d">';
			//характеристики товара
			if(! empty($result["param"]))
			{
				echo $this->get('param', 'shop', array("rows" => $result["param"], "id" => $result["id"]));
			}
			if(! empty($result["weight"]))
			{
				echo
				'<div class="param-d param-d_weight">
					<span class="param-d__name">'.$this->diafan->_('Вес').':</span>
					<strong class="param-d__value">'.$result["weight"].'</strong>
				</div>';
			}
			if(! empty($result["length"]))
			{
				echo
				'<div class="param-d param-d_length">
					<span class="param-d__name">'.$this->diafan->_('Длина').':</span>
					<strong class="param-d__value">'.$result["length"].'</strong>
				</div>';
			}
			if(! empty($result["width"]))
			{
				echo
				'<div class="param-d param-d_width">
					<span class="param-d__name">'.$this->diafan->_('Ширина').':</span>
					<strong class="param-d__value">'.$result["width"].'</strong>
				</div>';
			}
			if(! empty($result["height"]))
			{
				echo
				'<div class="param-d param-d_height">
					<span class="param-d__name">'.$this->diafan->_('Высота').':</span>
					<strong class="param-d__value">'.$result["height"].'</strong>
				</div>';
			}
			echo
		'</div>
		<div class="product-d__tab product-d__tab_comments tab-d">';
			//комментарии к товару
			if (!empty($result["comments"]))
			{
				echo $result["comments"];
			}
			echo
		'</div>
	</div>';
	echo
'</div>';

echo '</section>';

echo $this->htmleditor('<insert name="show_block_order_rel" module="shop" count="2" images="1" template="gall" defer="emergence" defer_title="C этим товаром покупают">');

echo $this->htmleditor('<insert name="show_block_rel" module="shop" count="4" images="1" template="gall" defer="emergence" defer_title="Похожие товары">');

echo $this->htmleditor('<insert name="show_block" module="bs" count="2" cat_id="2" template="banners">');

//ссылки на предыдущий и последующий товар
echo $this->htmleditor('<insert name="show_previous_next" module="shop">');

echo '</section>';
