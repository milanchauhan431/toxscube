$(document).ready(function(){
	$(document).on('click','.getPendingOrders',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_id :selected').text();
		$('.party_id').html("");

		if (party_id != "" || party_id != 0) {
			$.ajax({
				url: base_url + 'salesOrders/getPartyOrders',
				type: 'post',
				data: { party_id: party_id },
				success: function (response) {
					$("#modal-xl").modal();
					$('#modal-xl .modal-body').html('');
					$('#modal-xl .modal-title').html("Carete Invoice [ Party Name : "+party_name+" ]");
					$('#modal-xl .modal-body').html(response);
					$('#modal-xl .modal-body form').attr('id',"createInvoiceForm");
					$('#modal-xl .modal-footer .btn-save').html('<i class="fa fa-check"></i> Create Invoice');
					$("#modal-xl .modal-footer .btn-save").attr('onclick',"createInvoice();");
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}	
	});

    $(document).on('click', '.add-item', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('');
		$('#itemForm #row_index').val("");
		$('#itemForm #stock_eff').val("1");
        $("#itemForm .error").html();

		var party_id = $('#party_id').val();
		$(".party_id").html("");
		$("#itemForm #row_index").val("");
		if(party_id){
			setPlaceHolder();
			$("#itemModel").modal();
			$("#itemModel .btn-close").show();
			$("#itemModel .btn-save").show();	
            $("#itemForm .select2").select2();
			setTimeout(function(){ $("#itemForm #item_id").focus(); },500);	
		}else{ 
            $(".party_id").html("Party name is required."); $("#itemModel").modal('hide'); 
        }
	});

    $(document).on('click', '.saveItem', function () {
        
		var fd = $('#itemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			formData[v.name] = v.value;
		});
        $("#itemForm .error").html("");

        if (formData.item_id == "") {
			$(".item_id").html("Item Name is required.");
		}
		/* if (formData.item_name == "") {
			$(".item_name").html("Item Name is required.");
		} */
        if (formData.qty == "" || parseFloat(formData.qty) == 0) {
            $(".qty").html("Qty is required.");
        }
        if (formData.price == "" || parseFloat(formData.price) == 0) {
            $(".price").html("Price is required.");
        }

		if(formData.packing_qty == "" || parseInt(formData.packing_qty) == 0){
			$(".packing_qty").html("Packing standard is required.");
		}

        /* var item_ids = $(".item_id").map(function () { return $(this).val(); }).get();
        if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
            $(".item_name").html("Item already added.");
        } */

		if(parseFloat(formData.qty) > 0 && parseInt(formData.packing_qty) > 0){
			var totalBox = parseFloat(parseFloat(formData.qty) / parseFloat(formData.packing_qty));
			if(!Number.isInteger(totalBox)){
				$(".qty").html("Invalid qty against packing standard.");
			}
		}

        var errorCount = $('#itemForm .error:not(:empty)').length;

		if (errorCount == 0) {
            formData.disc_per = (parseFloat(formData.disc_per) > 0)?formData.disc_per:0;
            var amount = 0; var taxable_amount = 0; var disc_amt = 0; var igst_amt = 0;
            var gst_amount = 0; var cgst_amt = 0; var sgst_amt = 0; var net_amount = 0; 
            var gst_per = 0; var cgst_per = 0; var sgst_per = 0; var igst_per = 0;

			formData.org_price = (formData.org_price != "" || parseFloat(formData.org_price) > 0)?formData.org_price:0;
			formData.price = parseFloat((parseFloat(formData.org_price) * parseFloat(($("#master_i_col_1").val() || 0)) / 100)).toFixed(2);

            if (formData.disc_per == "" && formData.disc_per == "0") {
                taxable_amount = amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
            } else {
                amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
                disc_amt = parseFloat((amount * parseFloat(formData.disc_per)) / 100).toFixed(2);
                taxable_amount = parseFloat(amount - disc_amt).toFixed(2);
            }

            formData.gst_per = igst_per = parseFloat(formData.gst_per).toFixed(0);
            formData.gst_amount = igst_amt = parseFloat((igst_per * taxable_amount) / 100).toFixed(2);

            cgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);
            sgst_per = parseFloat(parseFloat(igst_per) / 2).toFixed(2);

            cgst_amt = parseFloat((cgst_per * taxable_amount) / 100).toFixed(2);
            sgst_amt = parseFloat((sgst_per * taxable_amount) / 100).toFixed(2);

            net_amount = parseFloat(parseFloat(taxable_amount) + parseFloat(igst_amt)).toFixed(2);

			formData.gst_per = parseFloat(formData.gst_per);
            formData.qty = parseFloat(formData.qty).toFixed(2);
            formData.cgst_per = cgst_per;
            formData.cgst_amount = cgst_amt;
            formData.sgst_per = sgst_per;
            formData.sgst_amount = sgst_amt;
            formData.igst_per = igst_per;
            formData.igst_amount = igst_amt;
            formData.disc_amount = disc_amt;
            formData.amount = amount;
            formData.taxable_amount = taxable_amount;
            formData.net_amount = net_amount;

            AddRow(formData);
            $('#itemForm')[0].reset();
            $("#itemForm input:hidden").val('')
            $('#itemForm #row_index').val("");
            $('#itemForm #stock_eff').val(1);
            $("#itemForm .select2").select2();
            if ($(this).data('fn') == "save") {
                $("#item_id").focus();
            } else if ($(this).data('fn') == "save_close") {
                $("#itemModel").modal('hide');
            }

        }
	});

    $(document).on('click', '.btn-close', function () {
		$('#itemForm')[0].reset();
		$("#itemForm input:hidden").val('')
		$('#itemForm #row_index').val("");
		$("#itemForm .error").html("");
        $("#itemForm .select2").select2();
	});  
	
	$(document).on('keyup change',"#itemForm #price",function(){
		var price = $(this).val() || 0;
		$("#itemForm #org_price").val(price);
	});

	$(document).on('change','#unit_id',function(){
		$("#unit_name").val("");
		if($(this).val()){ $("#unit_name").val($("#unit_id :selected").text()); }
	});

	$(document).on('change','#hsn_code',function(){
		$("#gst_per").val(($("#hsn_code :selected").data('gst_per') || 0));
		$("#gst_per").select2();
	});

	//on change Bill Per.
	$(document).on('keyup change','#master_i_col_1',function(){
		var billPer = $(this).val();
		billPer = (parseFloat(billPer) > 0)?billPer:0;
		var rowCount = $('#salesInvoiceItems tbody tr').length;
		var countRow = 0;
		for(var i=0;i<rowCount;i++){
			countRow = i + 1;
			if($("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][org_price]']").val()){				
				formData = {};				

				formData.id = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][id]']").val();
				formData.item_id = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_id]']").val();
				formData.item_name = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_name]']").val();
				formData.from_entry_type = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][from_entry_type]']").val();
				formData.ref_id = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][ref_id]']").val();
				formData.item_code = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_code]']").val();
				formData.item_type = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_type]']").val();
				formData.stock_eff = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][stock_eff]']").val();
				formData.p_or_m = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][p_or_m]']").val();
				formData.hsn_code = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][hsn_code]']").val();
				formData.qty = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][qty]']").val();
				formData.packing_qty = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][packing_qty]']").val();
				formData.unit_id = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][unit_id]']").val();
				formData.unit_name = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][unit_name]']").val();

				var org_price = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][org_price]']").val();
				formData.org_price = (org_price != "" || parseFloat(org_price) > 0)?org_price:0;
				formData.price = parseFloat((parseFloat(org_price) * parseFloat(billPer) / 100)).toFixed(2);
				
				formData.disc_per = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][disc_per]']").val();
				formData.gst_per = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][gst_per]']").val();
				formData.item_remark = $("#salesInvoiceItems tbody tr:eq("+i+") input[name='itemData["+countRow+"][item_remark]']").val();
				
				
				var cgst_amt = 0;var sgst_amt = 0;var igst_amt = 0;
				var cgst_per = 0;var sgst_per = 0;var igst_per = 0;
				var amount = 0;var disc_amt = 0;var total = 0;var net_amount = 0;
				if (formData.disc_per == "" && formData.disc_per == "0") {
					taxable_amount = amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
				} else {
					amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
					disc_amt = parseFloat((amount * parseFloat(formData.disc_per)) / 100).toFixed(2);
					taxable_amount = parseFloat(amount - disc_amt).toFixed(2);
				}
				
				cgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
				sgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
				
				cgst_amt = parseFloat((cgst_per * taxable_amount )/100).toFixed(2);
				sgst_amt = parseFloat((sgst_per * taxable_amount )/100).toFixed(2);
				
				formData.gst_per = igst_per = parseFloat(formData.gst_per).toFixed(2);
				formData.gst_amount = igst_amt = parseFloat((igst_per * taxable_amount )/100).toFixed(2);
				
				net_amount = parseFloat(parseFloat(taxable_amount) + parseFloat(igst_amt)).toFixed(2);

				formData.gst_per = parseFloat(formData.gst_per);
				formData.gst_type = $('#gst_type').val();
				formData.qty = parseFloat(formData.qty).toFixed(2);
				formData.cgst_per = cgst_per;
				formData.cgst_amount = cgst_amt;
				formData.sgst_per = sgst_per;
				formData.sgst_amount = sgst_amt;
				formData.igst_per = igst_per;
				formData.igst_amount = igst_amt;
				formData.disc_amount = disc_amt;
				formData.amount = amount;
				formData.taxable_amount = taxable_amount;
				formData.net_amount = net_amount;
				formData.row_index = ""+i+"";
				AddRow(formData); 				
			}
		}
	});
});

function createInvoice(){
	var mainRefIds = []; var mainFromEntryType = [];
	$(".orderItem:checked").map(function() {
		row = $(this).data('row');
		mainRefIds.push(row.trans_main_id);
		mainFromEntryType.push(row.main_entry_type);
		row.qty = row.pending_qty;
		row.gst_per = parseFloat(row.gst_per);
		row.org_price = row.price;
		AddRow(row);
	}).get();

	mainRefIds = $.unique(mainRefIds);
	mainFromEntryType = $.unique(mainFromEntryType);

	mainRefIds = mainRefIds.join(",");
	mainFromEntryType = mainFromEntryType.join(",");

	$("#saveSalesInvoice #ref_id").val("");
	$("#saveSalesInvoice #ref_id").val(mainRefIds);
	$("#saveSalesInvoice #from_entry_type").val("");
	$("#saveSalesInvoice #from_entry_type").val(mainFromEntryType);

	$("#modal-xl").modal('hide');
	$('#modal-xl .modal-body').html('');
}

function AddRow(data) {
    var tblName = "salesInvoiceItems";

    //Remove blank line.
	$('table#'+tblName+' tr#noData').remove();

	//Get the reference of the Table's TBODY element.
	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

    //Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

    var idInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][id]", value: data.id });
    var itemIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_id]", class:"item_id", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_name]", value: data.item_name });
    var formEnteryTypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][from_entry_type]", value: data.from_entry_type });
	var refIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][ref_id]", value: data.ref_id });
    var itemCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_code]", value: data.item_code });
    var itemtypeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_type]", value: data.item_type });
	var stockEffInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][stock_eff]", value: data.stock_eff });
    var pormInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][p_or_m]", value: -1 });
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(idInput);
    cell.append(itemIdInput);
    cell.append(itemNameInput);
    cell.append(formEnteryTypeInput);
    cell.append(refIdInput);
    cell.append(itemCodeInput);
    cell.append(itemtypeInput);
	cell.append(stockEffInput);
    cell.append(pormInput);

    var hsnCodeInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][hsn_code]", value: data.hsn_code });
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);

    var qtyInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][qty]", class:"item_qty", value: data.qty });
    var psInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][packing_qty]", value: data.packing_qty });
	var qtyErrorDiv = $("<div></div>", { class: "error qty" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(psInput);
	cell.append(qtyErrorDiv);

    var unitIdInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][unit_id]", value: data.unit_id });
	var unitNameInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][unit_name]", value: data.unit_name });
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);

    var priceInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][price]", value: data.price});
    var orgPriceInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][org_price]", value: data.org_price});
	var priceErrorDiv = $("<div></div>", { class: "error price" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(orgPriceInput);
	cell.append(priceErrorDiv);

    var discPerInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][disc_per]", value: data.disc_per});
	var discAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][disc_amount]", value: data.disc_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amount + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);

    var cgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][cgst_per]", value: data.cgst_per });
	var cgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][cgst_amount]", class:'cgst_amount', value: data.cgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amount + '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class", "cgstCol");

	var sgstPerInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][sgst_per]", value: data.sgst_per });
	var sgstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][sgst_amount]", class:"sgst_amount", value: data.sgst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amount + '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class", "sgstCol");

	var gstPerInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gst_per]", class:"gst_per", value: data.gst_per });
	var igstPerInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][igst_per]", value: data.igst_per });
	var gstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][gst_amount]", class:"gst_amount", value: data.gst_amount });
	var igstAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][igst_amount]", class:"igst_amount", value: data.igst_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amount + '(' + data.igst_per + '%)');
	cell.append(gstPerInput);
	cell.append(igstPerInput);
	cell.append(gstAmtInput);
	cell.append(igstAmtInput);
	cell.attr("class", "igstCol");

    var amountInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][amount]", class:"amount", value: data.amount });
    var taxableAmountInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][taxable_amount]", class:"taxable_amount", value: data.taxable_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.taxable_amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.attr("class", "amountCol");

	var netAmtInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][net_amount]", value: data.net_amount });
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class", "netAmtCol");

    var itemRemarkInput = $("<input/>", { type: "hidden", name: "itemData["+countRow+"][item_remark]", value: data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

    var gst_type = $("#gst_type").val();
	if (gst_type == 1) {
		$(".cgstCol").show(); $(".sgstCol").show(); $(".igstCol").hide();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else if (gst_type == 2) {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").show();
		$(".amountCol").hide(); $(".netAmtCol").show();
	} else {
		$(".cgstCol").hide(); $(".sgstCol").hide(); $(".igstCol").hide();
		$(".amountCol").show(); $(".netAmtCol").hide();
	}

    claculateColumn();
}

function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
	//$("#itemModel .btn-close").hide();
	$("#itemModel .btn-save").hide();
	$.each(data, function (key, value) {
		$("#itemForm #" + key).val(value);
	});
	$("#itemForm #price").val(data.org_price);
	$("#itemForm .select2").select2();
	$("#itemForm #row_index").val(row_index);
}

function Remove(button) {
    var tableId = "salesInvoiceItems";
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#"+tableId)[0];
	table.deleteRow(row[0].rowIndex);
	$('#'+tableId+' tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#'+tableId+' tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="15" align="center">No data available in table</td></tr>');
	}

	claculateColumn();
}

function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
        $("#party_name").val(partyDetail.party_name);
        $("#master_t_col_1").val(partyDetail.contact_person);
        $("#master_t_col_2").val(partyDetail.party_mobile);
        $("#master_t_col_3").val(partyDetail.delivery_address);

        var gstDetails = response.data.gstDetails; var i = 1;
        $.each(gstDetails,function(index,row){  
			if(row.gstin !=""){
				html += '<option value="'+row.gstin+'" '+((i==1)?"selected":"")+'>'+row.gstin+'</option>';
				i++;
			}            
        });        
    }else{
        $("#party_name").val("");
		$("#master_t_col_1").val("");
        $("#master_t_col_2").val("");
        $("#master_t_col_3").val("");
    }
    $("#gstin").html(html);$("#gstin").select2();gstin();
}

function resItemDetail(response = ""){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#itemForm #item_code").val(itemDetail.item_code);
        $("#itemForm #item_name").val(itemDetail.item_name);
        $("#itemForm #item_type").val(itemDetail.item_type);
        $("#itemForm #unit_id").val(itemDetail.unit_id);$("#itemForm #unit_id").select2();
        $("#itemForm #unit_name").val(itemDetail.unit_name);
		$("#itemForm #disc_per").val(itemDetail.defualt_disc);
		$("#itemForm #price").val(itemDetail.price);
		$("#itemForm #org_price").val(itemDetail.price);
		$("#itemForm #packing_qty").val(itemDetail.packing_standard);
        $("#itemForm #hsn_code").val(itemDetail.hsn_code);$("#itemForm #hsn_code").select2();
        $("#itemForm #gst_per").val(parseFloat(itemDetail.gst_per).toFixed(0));$("#itemForm #gst_per").select2();
    }else{
        $("#itemForm #item_code").val("");
        $("#itemForm #item_name").val("");
        $("#itemForm #item_type").val("");
        $("#itemForm #unit_id").val("");$("#itemForm #unit_id").select2();
        $("#itemForm #unit_name").val("");
		$("#itemForm #disc_per").val("");
		$("#itemForm #price").val("");
		$("#itemForm #org_price").val("");
		$("#itemForm #packing_qty").val("");
        $("#itemForm #hsn_code").val("");$("#itemForm #hsn_code").select2();
        $("#itemForm #gst_per").val(0);$("#itemForm #gst_per").select2(); 
    }
}

function resSaveInvoice(data,formId){
    if(data.status==1){
        $('#'+formId)[0].reset();
        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

        window.location = base_url + controller;
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) {$("."+key).html(value);});
        }else{
            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }			
    }	
}