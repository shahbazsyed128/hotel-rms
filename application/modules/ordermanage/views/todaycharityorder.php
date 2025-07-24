<table class="table table-fixed table-bordered table-hover bg-white wpr_100" id="onprocessingCharity">
    <thead>
        <tr>
            <th class="text-center">Date</th>
            <th class="text-center">Orders</th>
            <th class="text-center">Sells</th>
            <th class="text-right"><?php echo display('amount'); ?></th>
            <th class="text-center"><?php echo display('action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo date('d-m-Y'); ?></td>
            <td>Hassan</td>
            <td>Hassan</td>
            <td>Hassan</td>
            <td>Hassan</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right">Total:</th>
            <th colspan="1" class="text-center"></th>
        </tr>
    </tfoot>
</table>

<!-- Modal HTML -->
<div id="daily-report-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Daily Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="resultmsg"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><button type="button" style="padding: 10px;" id="get-daily-report" class="btn btn-primary " >Get Daily Report</button>

<!-- Modal HTML -->
<div id="daily-report-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel">Daily Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- <script src="<?php echo base_url('application/modules/ordermanage/assets/js/todaycharityorder.js'); ?>" type="text/javascript"></script> --> 
<script src="<?php echo base_url('application/modules/ordermanage/assets/js/expensesmodal.js'); ?>" type="text/javascript"></script>
