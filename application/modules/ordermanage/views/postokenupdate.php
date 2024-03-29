<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Printable area start -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Print Invoice</title>
	<style>
		@media print {
			body {
				font-weight: bold;
			}

			.section {
				page-break-after: always;
			}
		}
	</style>
	<script type="text/javascript">
		var pstatus = "<?php echo $this->uri->segment(5); ?>";
		if (pstatus == 0) {
			var returnurl = "<?php echo base_url('ordermanage/order/pos_invoice'); ?>";
		} else {
			var returnurl = "<?php echo base_url('ordermanage/order/pos_invoice'); ?>?tokenorder=<?php echo $orderinfo->order_id; ?>";
		}
		window.print();
		setInterval(function() {
			document.location.href = returnurl;
		}, 3000);
	</script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/modules/ordermanage/assets/css/pos_token.css'); ?>">
</head>

<body>

	<?php


	function formatNumber($number)
	{
		// Check if the number is an integer
		if ($number == intval($number)) {
			// If it's an integer, convert it to an integer
			return intval($number);
		} else {
			// If it's not an integer, keep the decimal part
			return rtrim(sprintf('%.8F', $number), '0');
		}
	}


	$itemsByKitchen = [];
	foreach ($iteminfo as $iteminf) {
		$itemsByKitchen[$iteminf->kitchenid]['iteminfo'][] = $iteminf;
	}

	foreach ($exitsitem as $exititem) {
		$itemsByKitchen[$exititem->kitchenid]['exitsitem'][] = $exititem;
	}


	$tokenHeader = "<div id='printableArea' class='print_area section'>
	<div class='panel-body'>
		<div class='table-responsive m-b-20'>
			<table border='0' class='font-18 wpr_100' style='width:100%; font-size:18px;'>
				<tr>
					<td>
						<table border='0' class='wpr_100' style='width:100%'>
							<tr>
								<td align='center'>
									<nobr>
										<date>" . display('token_no') . " : " . $orderinfo->tokenno . "
									</nobr><br />" . $customerinfo->customer_name . "
								</td>
							</tr>
						</table>
						<table width='100%'>
							<tr>
								<td>Q</th>
								<td>" . display('item') . "</td>
								<td>" . display('size') . "</td>
							</tr>";

	$tokenFooter = "<tr>
					<td colspan='5' class='border-top-gray'>
						<nobr></nobr>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align='center'>" . ((!empty($tableinfo)) ? (display('table') . ': ' . $tableinfo->tablename) : "") . " | " . display('ord_number') . ":" . $orderinfo->order_id . "</td>
	</tr>
</table>
</div>
</div>
</div>";






	foreach ($itemsByKitchen as $allItems) {
		$i = 0;
		$totalamount = 0;
		$subtotal = 0;
		$total = $orderinfo->totalamount;

		$itemcontent = "";

		foreach ($allItems['iteminfo'] as $item) {
			$i++;
			$itemprice = $item->price * $item->menuqty;
			$discount = 0;
			$adonsprice = 0;
			$newitem = $this->order_model->read('*', 'order_menu', array('row_id' => $item->row_id, 'isupdate' => 1));
			$isexitsitem = $this->order_model->readupdate('tbl_updateitems.*,SUM(tbl_updateitems.qty) as totalqty', 'tbl_updateitems', array('ordid' => $item->order_id, 'menuid' => $item->menu_id, 'varientid' => $item->varientid, 'addonsuid' => $item->addonsuid));
			if (!empty($item->add_on_id)) {
				$addons = explode(",", $item->add_on_id);
				$addonsqty = explode(",", $item->addonsqty);
				$x = 0;
				foreach ($addons as $addonsid) {
					$adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
					$adonsprice = $adonsprice + $adonsinfo->price * $addonsqty[$x];
					$x++;
				}
				$nittotal = $adonsprice;
				$itemprice = $itemprice;
			} else {
				$nittotal = 0;
				$text = '';
			}
			$totalamount = $totalamount + $nittotal;
			$subtotal = $subtotal + $item->price * $item->menuqty;
			if ($newitem->menu_id == $isexitsitem->menuid && $newitem->isupdate == 1) {
				$itemcontent .= "<tr>
						<td align='left'>" . $item->menuqty . "</td>
						<td align='left'>" . $item->ProductName . "<br>" . $item->notes . "</td>
						<td align='left'>" . $item->variantName . "</td>
					</tr>";
			} else {
				$itemcontent .= "<tr>
						<td align='left'>" . $item->menuqty . "</td>
						<td align='left'>" . $item->ProductName . "<br>" . $item->notes . "</td>
						<td align='left'>" . $item->variantName . "</td>
					</tr>";
			}
		}


		$content = "";
		$i = 0;
		$totalamount = 0;
		$subtotal = 0;
		$total = $orderinfo->totalamount;
		$itemtotal = $totalamount + $subtotal;
		$calvat = $itemtotal * 15 / 100;

		$servicecharge = 0;
		if (empty($billinfo)) {
			$servicecharge;
		} else {
			$servicecharge = $billinfo->service_charge;
		}

		foreach ($allItems['exitsitem'] as $exititem) {
			$newitem = $this->order_model->read('*', 'order_menu', array('row_id' => $exititem->row_id, 'isupdate' => 1));
			$isexitsitem = $this->order_model->readupdate('tbl_updateitems.*,SUM(tbl_updateitems.qty) as totalqty', 'tbl_updateitems', array('ordid' => $orderinfo->order_id, 'menuid' => $exititem->menu_id, 'varientid' => $exititem->varientid, 'addonsuid' => $exititem->addonsuid));
			if (!empty($isexitsitem)) {
				if ($isexitsitem->qty > 0) {
					$itemprice = $exititem->price * $isexitsitem->qty;
					if ($newitem->isupdate == 1) {
						echo "";
					} else {

						$content .= "<tr>
											<td align='left'>" . $isexitsitem->isupdate . " " . formatNumber($isexitsitem->totalqty) . " </td>
											<td align='left'>" . $exititem->ProductName . " " . $exititem->notes . "</td>
											<td align='left'>" . $exititem->variantName . "</td>
										</tr>";
					}
				}
			} else {
			}
		}


		if (!empty($content || !empty($itemcontent))) {
			echo $tokenHeader;
			echo $content;
			echo  $itemcontent;
			echo $tokenFooter;
			$content = "";
			$itemcontent = "";
		}
	}
	?>
</body>

</html>