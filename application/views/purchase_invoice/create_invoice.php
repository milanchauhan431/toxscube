<form>
    <div class="col-md-12">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>GI No.</th>
                            <th>GI Date</th>
                            <th>Inv No.</th>
                            <th>Challan No.</th>
                            <th>Item Name</th>
                            <th>Pending Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1;
                            foreach($orderItems as $row):
                                $row->from_entry_type = $row->entry_type;
                                $row->ref_id = $row->id;
                                unset($row->id,$row->entry_type);
                                $row->row_index = "";
                                $row->entry_type = "";

                                $row->amount = (!empty($row->price))?($row->qty * $row->price):0;
                                $row->disc_amount = (!empty($row->disc_per))?(($row->amount * $row->disc_per) / 100):0;
                                $row->taxable_amount = $row->amount - $row->disc_amount;
                                $row->gst_amount = $row->igst_amount = (!empty($row->gst_per))?(($row->taxable_amount * $row->gst_per) / 100):0;
                                $row->cgst_per = $row->sgst_per = (!empty($row->gst_per))?($row->gst_per / 2):0;
                                $row->igst_per = (!empty($row->gst_per))?$row->gst_per:0;
                                $row->cgst_amount = $row->sgst_amount = (!empty($row->igst_amount))?($row->igst_amount/2):0;
                                $row->net_amount = $row->taxable_amount + $row->igst_amount;

                                echo "<tr>
                                    <td class='text-center'>
                                        <input type='checkbox' id='md_checkbox_" . $i . "' class='filled-in chk-col-success orderItem' data-row='".json_encode($row)."' ><label for='md_checkbox_" . $i . "' class='mr-3 check" . $row->ref_id . "'></label>
                                    </td>
                                    <td>".$row->trans_number."</td>
                                    <td>".formatDate($row->trans_date)."</td>
                                    <td>".$row->inv_no."</td>
                                    <td>".$row->doc_no."</td>
                                    <td>".$row->item_name."</td>
                                    <td>".floatval($row->pending_qty)."</td>
                                </tr>";
                                $i++;
                            endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>