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


	$itemsByKitchen = [];

	// Check if $iteminfo is not empty, use $iteminfo, otherwise use $exitsitem
	if (!empty($iteminfo)) {
		foreach ($iteminfo as $item) {
			$itemsByKitchen[$item->kitchenid][] = $item;
		}
		// Set the loop variable name based on whether $iteminfo is empty or not
		$loopVariableName = 'iteminfo';
	} else {
		foreach ($exitsitem as $exititem) {
			$itemsByKitchen[$exititem->kitchenid][] = $exititem;
		}
		// Set the loop variable name based on whether $iteminfo is empty or not
		$loopVariableName = 'exitsitem';
	}

	?>

	<?php foreach ($itemsByKitchen as $$loopVariableName) {

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

		foreach ($exitsitem as $exititem) {
			$newitem = $this->order_model->read('*', 'order_menu', array('row_id' => $exititem->row_id, 'isupdate' => 1));

			$isexitsitem = $this->order_model->readupdate('tbl_updateitems.*,SUM(tbl_updateitems.qty) as totalqty', 'tbl_updateitems', array('ordid' => $orderinfo->order_id, 'menuid' => $exititem->menu_id, 'varientid' => $exititem->varientid, 'addonsuid' => $exititem->addonsuid));
			if (!empty($isexitsitem)) {
				if ($isexitsitem->qty > 0) {
					$itemprice = $exititem->price * $isexitsitem->qty;
					if ($newitem->isupdate == 1) {
						echo "";
					} else { ?>

						<div id="printableArea" class="print_area section">
							<div class="panel-body">
								<div class="table-responsive m-b-20">
									<table border="0" class="font-18 wpr_100" style="width:100%; font-size:18px;">
										<tr>
											<td>

												<table border="0" class="wpr_100" style="width:100%">

													<tr>
														<td align="center">
															<nobr>
																<date><?php echo display('token_no') ?>:<?php echo $orderinfo->tokenno; ?>
															</nobr><br /><?php echo $customerinfo->customer_name; ?>
														</td>
													</tr>
												</table>
												<table width="100%">
													<tr>
														<td>Q</th>
														<td><?php echo display('item') ?></td>
														<td><?php echo display('size') ?></td>
													</tr>
													<tr>
														<td align="left"><?php echo $isexitsitem->isupdate; ?> <?php echo $isexitsitem->totalqty; ?></td>
														<td align="left"><?php echo $exititem->ProductName; ?><br><?php echo $exititem->notes; ?></td>
														<td align="left"><?php echo $exititem->variantName; ?></td>
													</tr>

													<tr>
														<td colspan="5" class="border-top-gray">
															<nobr></nobr>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td align="center"><?php if (!empty($tableinfo)) {
																	echo display('table') . ': ' . $tableinfo->tablename;
																} ?> | <?php echo display('ord_number'); ?>:<?php echo $orderinfo->order_id; ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div><?php
							}
						}
					} else {
					}
				}
			}
								?>
</body>

</html>