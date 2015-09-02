<?PHP
require_once('api/Simpla.php');

class UserAdmin extends Simpla
{	
	public function fetch()
	{
		$user = new stdClass;
		if(!empty($_POST['user_info']))
			{
			$user->id = $this->request->post('id', 'integer');
			$user->enabled = $this->request->post('enabled');
			$user->name = $this->request->post('name');
			$user->email = $this->request->post('email');
			$user->password = $this->request->post('password');
			
			//@ new table SQL

            $user->rukovoditel = $this->request->post('rukovoditel');
            $user->rukovoditeltel = $this->request->post('rukovoditeltel');
            $user->snab = $this->request->post('snab');
            $user->snabtel = $this->request->post('snabtel');
            $user->site = $this->request->post('site');
            $user->gorod = $this->request->post('gorod');
            $user->adress = $this->request->post('adress');

			$user->group_id = $this->request->post('group_id');

			//персональные цены

			$user->productid = $this->request->post('productid');
			$user->userid = $this->request->post('userid');
			$user->uprice = $this->request->post('uprice');

			$user->id = $this->users->add_uprice(array('productid'=>$user->productid,'userid'=>$user->userid));

			## Не допустить одинаковые email пользователей.
			if(empty($user->name))
			{			
				$this->design->assign('message_error', 'empty_name');
			}
			elseif(empty($user->id))
			{			
				$user->id = $this->users->add_user(array('name'=>$user->name,'email'=>$user->email,'rukovoditel'=>$user->rukovoditel,'rukovoditeltel'=>$user->rukovoditeltel,'snab'=>$user->snab,'snabtel'=>$user->snabtel,'site'=>$user->site,'gorod'=>$user->gorod,'adress'=>$user->adress));
				$this->design->assign('message_success', 'Новый покупатель успешно создан!:)');
			}
			elseif($user->id)//исправлен баг с редактированием
			{
				$user->id = $this->users->update_user($user->id, $user);
				$this->design->assign('message_success', 'updated');
   	    		$user = $this->users->get_user(intval($user->id));

			}
		}
		elseif($this->request->post('check'))
		{ 
			// Действия с выбранными
			$ids = $this->request->post('check');
			if(is_array($ids))
			switch($this->request->post('action'))
			{
				case 'delete':
				{
					foreach($ids as $id)
						$o = $this->orders->get_order(intval($id));
						if($o->status<3)
						{
							$this->orders->update_order($id, array('status'=>3, 'user_id'=>null));
							$this->orders->open($id);							
						}
						else
							$this->orders->delete_order($id);
					break;
				}
			}
 		}

		$id = $this->request->get('id', 'integer');
		if(!empty($id))
			$user = $this->users->get_user(intval($id));
		    


		if(!empty($user))
		{
			$this->design->assign('user', $user);
			
			$orders = $this->orders->get_orders(array('user_id'=>$user->id));
			$this->design->assign('orders', $orders);
			
		}
		
 	  	

 	  	//test)))

 	  	// Отображение
		$filter = array();
		$filter['page'] = max(1, $this->request->get('page', 'integer'));
			
		$filter['limit'] = $this->settings->products_num_admin;
	
		// Категории
		$categories = $this->categories->get_categories_tree();
		$this->design->assign('categories', $categories);
		
		// Текущая категория
		$category_id = $this->request->get('category_id', 'integer'); 
		if($category_id && $category = $this->categories->get_category($category_id))
	  		$filter['category_id'] = $category->children;
		      
		// Бренды категории
		$brands = $this->brands->get_brands(array('category_id'=>$category_id));
		$this->design->assign('brands', $brands);
		
		// Все бренды
		$all_brands = $this->brands->get_brands();
		$this->design->assign('all_brands', $all_brands);
		
		// Текущий бренд
		$brand_id = $this->request->get('brand_id', 'integer'); 
		if($brand_id && $brand = $this->brands->get_brand($brand_id))
			$filter['brand_id'] = $brand->id;
	
		// Текущий фильтр
		if($f = $this->request->get('filter', 'string'))
		{
			if($f == 'featured')
				$filter['featured'] = 1; 
			elseif($f == 'discounted')
				$filter['discounted'] = 1; 
			elseif($f == 'visible')
				$filter['visible'] = 1; 
			elseif($f == 'hidden')
				$filter['visible'] = 0; 
			elseif($f == 'outofstock')
				$filter['in_stock'] = 0; 
			$this->design->assign('filter', $f);
		}
	
		// Поиск
		$keyword = $this->request->get('keyword');
		if(!empty($keyword))
		{
	  		$filter['keyword'] = $keyword;
			$this->design->assign('keyword', $keyword);
		}
			
		// Обработка действий 	
		if($this->request->method('post'))
		{
			// Сохранение цен и наличия
			$prices = $this->request->post('price');
			$stocks = $this->request->post('stock');
		
			foreach($prices as $id=>$price)
			{
				$stock = $stocks[$id];
				if($stock == '∞' || $stock == '')
					$stock = null;
					
				$this->variants->update_variant($id, array('price'=>$price, 'stock'=>$stock));
			}
		
			// Сортировка
			$positions = $this->request->post('positions'); 		
				$ids = array_keys($positions);
			sort($positions);
			$positions = array_reverse($positions);
			foreach($positions as $i=>$position)
				$this->products->update_product($ids[$i], array('position'=>$position)); 
		
			
			// Действия с выбранными
			$ids = $this->request->post('check');
			if(!empty($ids))
			switch($this->request->post('action'))
			{
			    case 'disable':
			    {
			    	$this->products->update_product($ids, array('visible'=>0));
					break;
			    }
			    case 'enable':
			    {
			    	$this->products->update_product($ids, array('visible'=>1));
			        break;
			    }
			    case 'set_featured':
			    {
			    	$this->products->update_product($ids, array('featured'=>1));
					break;
			    }
			    case 'unset_featured':
			    {
			    	$this->products->update_product($ids, array('featured'=>0));
					break;
			    }
			    case 'set_yandex':
{
    $this->products->update_product($ids, array('to_yandex'=>1));
    break;
}
case 'unset_yandex':
{
    $this->products->update_product($ids, array('to_yandex'=>0));
    break;
}
			    case 'delete':
			    {
				    foreach($ids as $id)
						$this->products->delete_product($id);    
			        break;
			    }
			    case 'duplicate':
			    {
				    foreach($ids as $id)
				    	$this->products->duplicate_product(intval($id));
			        break;
			    }
			    case 'move_to_page':
			    {
		
			    	$target_page = $this->request->post('target_page', 'integer');
			    	
			    	// Сразу потом откроем эту страницу
			    	$filter['page'] = $target_page;
		
				    // До какого товара перемещать
				    $limit = $filter['limit']*($target_page-1);
				    if($target_page > $this->request->get('page', 'integer'))
				    	$limit += count($ids)-1;
				    else
				    	$ids = array_reverse($ids, true);
		

					$temp_filter = $filter;
					$temp_filter['page'] = $limit+1;
					$temp_filter['limit'] = 1;
					$target_product = array_pop($this->products->get_products($temp_filter));
					$target_position = $target_product->position;
				   	
				   	// Если вылезли за последний товар - берем позицию последнего товара в качестве цели перемещения
					if($target_page > $this->request->get('page', 'integer') && !$target_position)
					{
				    	$query = $this->db->placehold("SELECT distinct p.position AS target FROM __products p LEFT JOIN __products_categories AS pc ON pc.product_id = p.id WHERE 1 $category_id_filter $brand_id_filter ORDER BY p.position DESC LIMIT 1", count($ids));	
				   		$this->db->query($query);
				   		$target_position = $this->db->result('target');
					}
				   	
			    	foreach($ids as $id)
			    	{		    	
				    	$query = $this->db->placehold("SELECT position FROM __products WHERE id=? LIMIT 1", $id);	
				    	$this->db->query($query);	      
				    	$initial_position = $this->db->result('position');
		
				    	if($target_position > $initial_position)
				    		$query = $this->db->placehold("	UPDATE __products set position=position-1 WHERE position>? AND position<=?", $initial_position, $target_position);	
				    	else
				    		$query = $this->db->placehold("	UPDATE __products set position=position+1 WHERE position<? AND position>=?", $initial_position, $target_position);	
				    		
			    		$this->db->query($query);	      			    	
			    		$query = $this->db->placehold("UPDATE __products SET __products.position = ? WHERE __products.id = ?", $target_position, $id);	
			    		$this->db->query($query);	
				    }
			        break;
				}
			    case 'move_to_category':
			    {
			    	$category_id = $this->request->post('target_category', 'integer');
			    	$filter['page'] = 1;
					$category = $this->categories->get_category($category_id);
	  				$filter['category_id'] = $category->children;
			    	
			    	foreach($ids as $id)
			    	{
			    		$query = $this->db->placehold("DELETE FROM __products_categories WHERE category_id=? AND product_id=? LIMIT 1", $category_id, $id);	
			    		$this->db->query($query);	      			    	
			    		$query = $this->db->placehold("UPDATE IGNORE __products_categories set category_id=? WHERE product_id=? ORDER BY position DESC LIMIT 1", $category_id, $id);	
			    		$this->db->query($query);
			    		if($this->db->affected_rows() == 0)
							$query = $this->db->query("INSERT IGNORE INTO __products_categories set category_id=?, product_id=?", $category_id, $id);	

				    }
			        break;
				}
			    case 'move_to_brand':
			    {
			    	$brand_id = $this->request->post('target_brand', 'integer');
			    	$brand = $this->brands->get_brand($brand_id);
			    	$filter['page'] = 1;
	  				$filter['brand_id'] = $brand_id;
			    	$query = $this->db->placehold("UPDATE __products set brand_id=? WHERE id in (?@)", $brand_id, $ids);	
			    	$this->db->query($query);	

					// Заново выберем бренды категории
					$brands = $this->brands->get_brands(array('category_id'=>$category_id));
					$this->design->assign('brands', $brands);
			    	      			    	
			        break;
				}
			}			
		}

		// Отображение
		if(isset($brand))
			$this->design->assign('brand', $brand);
		if(isset($category))
			$this->design->assign('category', $category);
		
	  	$products_count = $this->products->count_products($filter);
		// Показать все страницы сразу
		if($this->request->get('page') == 'all')
			$filter['limit'] = $products_count;
		
		if($filter['limit']>0)	  	
		  	$pages_count = ceil($products_count/$filter['limit']);
		else
		  	$pages_count = 0;
	  	$filter['page'] = min($filter['page'], $pages_count);
	 	$this->design->assign('products_count', $products_count);
	 	$this->design->assign('pages_count', $pages_count);
	 	$this->design->assign('current_page', $filter['page']);



	 	///////////////////////////////////

        $uprice = $this->users->get_uprice(array('userid'=>$user->id));
        //$rows = $this->users->get_uprice(array('userid'=>$user->id));
		
			//foreach($rows as &$row)
			//{

			//	echo $row->productid;
				//$products->uprice[] = "123";
			//}
		
        //$this->design->assign('row', $row);


	 	////////////////////////////////
	 	
		$products = array();
		foreach($this->products->get_products($filter) as $p)
			$products[$p->id] = $p;
		    //$products[$p->uprice] = "123";

		if(!empty($products))
		{
		  	
			// Товары 
			$products_ids = array_keys($products);
			foreach($products as &$product)
			{
				$product->variants = array();
				$product->images = array();
				$product->properties = array();
				$product->rows = array();
				$product->row = "123";
			}

			//////////////

            $rows = $this->users->get_uprice(array('userid'=>$user->id));

			foreach($rows as &$r)
			{
				$products[$r->productid]->rows[] = $r;
				//$products[$r->productid]->pclienta[] = $r;
				//echo $r->productid;

			}

			/////////////////


            $cclienta = $this->users->get_uprice(array('userid'=>$user->id, 'pricetype'=>'0'));

			foreach($cclienta as &$cc)
			{
				$products[$cpc->productid]->cclienta[] = $cpc;
				//echo $r->productid;
				echo "<h1>0</h1>";
				echo "<ul>";
				echo "<li>";
				echo $cc->date;
				echo "<li>";
				echo "</ul>";

			}

			//////////////////

			echo "</br>";

			 $cutv = $this->users->get_uprice(array('userid'=>$user->id, 'pricetype'=>'1'));

			foreach($cutv as &$ctv)
			{
				$products[$ctv->productid]->cutv[] = $ctv;
				//echo $r->productid;
				echo "<h1>0</h1>";
				echo "<ul>";
				echo "<li>";
				echo $ctv->date;
				echo "<li>";
				echo "</ul>";

			}

			//////////////////
		
			$variants = $this->variants->get_variants(array('product_id'=>$products_ids));
		
			foreach($variants as &$variant)
			{
				$products[$variant->product_id]->variants[] = $variant;
			}
		
			$images = $this->products->get_images(array('product_id'=>$products_ids));
			foreach($images as $image)
				$products[$image->product_id]->images[$image->id] = $image;
		}


		//$this->design->assign('rows', $rows);


        //$this->design->assign('uprice', $uprice);

        //
	 
		$this->design->assign('products', $products);
		$groups = $this->users->get_groups();
		$this->design->assign('groups', $groups);
		
 	  	return $this->design->fetch('user.tpl');
	}
	
}

