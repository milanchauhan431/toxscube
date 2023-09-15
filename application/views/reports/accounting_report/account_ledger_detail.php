<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
						    <input type="hidden" id="acc_id" value="<?=(!empty($acc_id))?$acc_id:''?>" />
							<div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right refreshReportData loadData" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>              
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive" style="width: 100%;">
                            <table id='commanTableDetail' class="table table-bordered" style="width:100%;">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
										<th colspan="2" class="text-left"><?=formatDate($startDate).' - '.formatDate($endDate)?></th>
										<th colspan="4" class="text-center"><?=$acc_name?></th>
										<th colspan="2" class="text-right">Opening Balance : <span id="op_balance">0.00</span></th>
									</tr>
									<tr>
										<th>#</th>
										<th>Vou. Date</th>
										<th>Particulars</th>
										<th>Voucher Type</th>
										<th>Vou. No.</th>
										<th>Amount(CR.)</th>
										<th>Amount(DR.)</th>
										<th>Balance</th>
									</tr>
								</thead>
								<tbody id="ledgerDetail">
								</tbody>  
                                <tfoot class="thead-info">
                                    <tr>
                                        <th colspan="5" class="text-right">Total</th>
                                        <th id="cr_balance">0.00</th>
                                        <th id="dr_balance">0.00</th>
										<th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" id="bank_cl"></th>
                                        <th colspan="4" class="text-right">Closing Balance : <span id="cl_balance">0.00</span></th>
                                    </tr>
                                </tfoot>                            
							</table>
                        </div>

                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
var dataTblBtns = ['pageLength', 'excel',{ text: 'Pdf',action: function () {loadData('pdf'); } } , {text: 'Refresh',action: function (){ $(".refreshReportData").trigger('click'); } }];

<?php if(in_array($ledgerData->group_code,["BA","BOL","BOA"])): ?>
    dataTblBtns = ['pageLength', 'excel',{text: 'Pdf',action: function ( e, dt, node, config ) {loadData('pdf');}}, {text: 'Refresh',action: function (){ $(".refreshReportData").trigger('click'); } },{text:'Reconciliation',action: function ( e, dt, node, config ) { loadReconciliation(); }}];
<?php endif; ?>

var tableOption = {
    responsive: true,
    "scrollY": '52vh',
    "scrollX": true,
    deferRender: true,
    scroller: true,
    destroy: true,
    "autoWidth" : false,
    order: [],
    "columnDefs": [
        {type: 'natural',targets: 0},
        {orderable: false,targets: "_all"},
        {className: "text-center",targets: [0, 1]},
        {className: "text-center","targets": "_all"}
    ],
    pageLength: 25,
    language: {search: ""},
    lengthMenu: [
        [ 10, 20, 25, 50, 75, 100, 250,500 ],
        [ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
    ],
    dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: dataTblBtns,
    "fnInitComplete":function(){ $('.dataTables_scrollBody').perfectScrollbar(); },
    "fnDrawCallback": function() { $('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar(); }
};

$(document).ready(function(){
	loadData();
    $(document).on('click','.loadData',function(){
		loadData();
	}); 
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var acc_id = $('#acc_id').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
    var postData = {acc_id:acc_id,from_date:from_date, to_date:to_date,pdf:pdf};
	if(valid){
        if(pdf == "")
		{
            $.ajax({
                url: base_url + controller + '/getLedgerTransaction',
                data: postData,
                type: "POST",
                dataType:'json',
                success:function(data){              
                    $("#commanTableDetail").DataTable().clear().destroy();
                    $("#ledgerDetail").html("");
                    $("#ledgerDetail").html(data.tbody);
                    
                    $("#op_balance").html("");
                    $("#op_balance").html(data.ledgerBalance.op_balance+" "+data.ledgerBalance.op_balance_type);

                    $("#cl_balance").html("");
                    $("#cl_balance").html(data.ledgerBalance.cl_balance+" "+data.ledgerBalance.cl_balance_type);

                    $("#cr_balance").html("");
                    $("#cr_balance").html(data.ledgerBalance.cr_balance);
                    $("#dr_balance").html("");
                    $("#dr_balance").html(data.ledgerBalance.dr_balance);

                    $("#bank_cl").html("");
                    $("#bank_cl").html(data.ledgerBalance.bcl_balance_text);
                    reportTable('commanTableDetail',tableOption);

                }
            });
        }
        else
		{
			var url = base_url + controller + '/getLedgerTransaction/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			window.open(url);
		}
	}
}

function loadReconciliation(){
	var acc_id = $('#acc_id').val();
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	window.open(base_url + 'paymentVoucher/bankReconciliation/'+acc_id+'/'+from_date+'/'+to_date);
}
</script>