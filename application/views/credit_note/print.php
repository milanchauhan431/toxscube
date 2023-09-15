<div class="row">
    <div class="col-12">
        <?php if(!empty($header_footer)): ?>
        <table>
            <tr>
                <td>
                    <img src="<?=$letter_head?>" class="img">
                </td>
            </tr>
        </table>
        <?php endif; ?>

        <table class="table bg-light-grey">
            <tr class="" style="letter-spacing: 2px;font-weight:bold;padding:2px !important; border-bottom:1px solid #000000;">
                <td style="width:33%;" class="fs-18 text-left">
                    GSTIN: <?=$companyData->company_gst_no?>
                </td>
                <td style="width:33%;" class="fs-18 text-center">
                    <?=($invData->order_type == "Sales Return")?"Sales Return":"Credit Note"?>
                </td>
                <td style="width:33%;" class="fs-18 text-right"><?=$printType?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb fs-22" style="margin-top:5px;">
            <tr>
                <td style="width:60%; vertical-align:top;" rowspan="5">
                    <b>M/S. <?=$invData->party_name?></b><br>
                    <?=(!empty($partyData->party_address) ? $partyData->party_address : '')?><br>
                    <b>GSTIN : <?= $invData->gstin?> | STATE CODE: <?=$invData->party_state_code?> | CITY : <?=$partyData->city_name?></b>
                </td>
                <td>
                    <b>CN No.</b>
                </td>
                <td>
                    <?=$invData->trans_number?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>CN Date</b>
                </td>
                <td>
                    <?=date('d/m/Y', strtotime($invData->trans_date))?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Memo Type</b>
                </td>
                <td>
                    <?=$invData->memo_type?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Inv. No.</b>
                </td>
                <td>
                    <?=$invData->doc_no?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Inv. Date</b>
                </td>
                <td>
                    <?=$invData->doc_date?>
                </td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:10px;">
            <?php $thead = '<thead>
                    <tr>
                        <th style="width:40px;">No.</th>
                        <th class="text-left">Description of Goods</th>
                        <th style="width:10%;">HSN/SAC</th>
                        <th style="width:100px;">Qty</th>
                        <th style="width:60px;">Rate<br><small>('.$partyData->currency.')</small></th>
                        <th style="width:60px;">Disc (%)</th>
                        <th style="width:60px;">GST <small>(%)</small></th>
                        <th style="width:110px;">Amount<br><small>('.$partyData->currency.')</small></th>
                    </tr>
                </thead>';
                echo $thead;
            ?>
            <tbody>
                <?php
                    $i=1;$totalQty = 0;$migst=0;$mcgst=0;$msgst=0;
                    $rowCount = 1;$pageCount = 1;
                    if(!empty($invData->itemList)):
                        foreach($invData->itemList as $row):						
                            echo '<tr>';
                                echo '<td class="text-center">'.$i++.'</td>';
                                echo '<td>'.$row->item_name.'</td>';
                                echo '<td class="text-center">'.$row->hsn_code.'</td>';
                                echo '<td class="text-center">'.floatVal($row->qty).' ('.$row->unit_name.')</td>';
                                echo '<td class="text-right">'.floatVal($row->price).'</td>';
                                echo '<td class="text-right">'.floatVal($row->disc_per).'</td>';
                                echo '<td class="text-center">'.$row->gst_per.'</td>';
                                echo '<td class="text-right">'.$row->taxable_amount.'</td>';
                            echo '</tr>';
                            
                            if(($rowCount == $maxLinePP && $pageCount == 1) || ($rowCount == 20 && $pageCount != 1)): 
                                echo '
                                    </tbody></table>
                                    <div class="text-right"><i>Continue to Next Page</i></div>
                                    <pagebreak>
                                    <table class="table item-list-bb" style="margin-top:10px;">
                                        '.$thead.'
                                    <tbody>'; 
                                $rowCount = 0; // Reset the row count
                                $pageCount++; // Increment the page count
                            endif;
                            $rowCount++;

                            $totalQty += $row->qty;
                            if($row->gst_per > $migst){$migst=$row->gst_per;$mcgst=$row->cgst_per;$msgst=$row->sgst_per;}
                        endforeach;
                    endif;

                    $blankLines = ($maxLinePP - $i);
                    if($blankLines > 0):
                        for($j=1;$j<=$blankLines;$j++):
                            echo '<tr>
                                <td style="border-top:none;border-bottom:none;">&nbsp;</td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                                <td style="border-top:none;border-bottom:none;"></td>
                            </tr>';
                        endfor;
                    endif;
                    
                    $rwspan= 0; $srwspan = '';
                    $beforExp = "";
                    $afterExp = "";
                    $invExpenseData = (!empty($invData->expenseData)) ? $invData->expenseData : array();
                    foreach ($expenseList as $row) :
                        $expAmt = 0;
                        $amtFiledName = $row->map_code . "_amount";
                        if (!empty($invExpenseData) && $row->map_code != "roff") :
                            $expAmt = floatVal($invExpenseData->{$amtFiledName});
                        endif;

                        if(!empty($expAmt)):
                            if ($row->position == 1) :
                                if($rwspan == 0):
                                    $beforExp .= '<th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>';
                                else:
                                    $beforExp .= '<tr>
                                        <th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                        <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                                    </tr>';
                                endif;                                
                            else:
                                $afterExp .= '<tr>
                                    <th colspan="2" class="text-right">'.$row->exp_name.'</th>
                                    <td class="text-right">'.sprintf('%.2f',$expAmt).'</td>
                                </tr>';
                            endif;
                            $rwspan++;
                        endif;
                    endforeach;

                    $taxHtml = '';
                    foreach ($taxList as $taxRow) :
                        $taxAmt = 0;
                        $taxAmt = floatVal($invData->{$taxRow->map_code.'_amount'});
                        if(!empty($taxAmt)):
                            if($rwspan == 0):
                                $taxHtml .= '<th colspan="2" class="text-right">'.$taxRow->name.' @'.(($invData->gst_type == 1)?floatVal($migst/2):$migst).'%</th>
                                <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>';
                            else:
                                $taxHtml .= '<tr>
                                    <th colspan="2" class="text-right">'.$taxRow->name.' @'.(($invData->gst_type == 1)?floatVal($migst/2):$migst).'%</th>
                                    <td class="text-right">'.sprintf('%.2f',$taxAmt).'</td>
                                </tr>';
                            endif;
                        
                            $rwspan++;
                        endif;
                    endforeach;
                ?>
                <tr>
                    <th colspan="3" class="text-right">Total Qty.</th>
                    <th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
                    <th></th>
                    <th colspan="2" class="text-right">Sub Total</th>
                    <th class="text-right"><?=sprintf('%.2f',$invData->taxable_amount)?></th>
                </tr>
                <tr >
                    <th class="text-left" colspan="5" rowspan="<?=$rwspan?>">
                        <b>Bank Name : </b> <?=$companyData->company_bank_name?><br>
                        <b>A/c. No. : </b><?=$companyData->company_acc_no?><br>
                        <b>IFSC Code : </b><?=$companyData->company_ifsc_code?><br>
                        <b>Branch : </b><?=$companyData->company_bank_branch?>
                    </th>
                </tr>
                <!-- <tr>
                    <th class="text-left" colspan="5" rowspan="2">
                        Notes : <br><?=$invData->remark?>
                    </th>				
                </tr> -->
                <?=$beforExp.$taxHtml.$afterExp?>
                <tr>
                    <th class="text-left" colspan="5" rowspan="3">
                        Amount In Words : <br><?=numToWordEnglish(sprintf('%.2f',$invData->net_amount))?>
                    </th>				
                </tr>
                
                <tr>
                    <th colspan="2" class="text-right">Round Off</th>
                    <td class="text-right"><?=sprintf('%.2f',$invData->round_off_amount)?></td>
                </tr>
                <tr>
                    <th colspan="2" class="text-right">Grand Total</th>
                    <th class="text-right"><?=sprintf('%.2f',$invData->net_amount)?></th>
                </tr>
            </tbody>
        </table>

        <h4>Terms & Conditions :-</h4>
        <table class="table top-table" style="margin-top:10px;">
            <?php
                if(!empty($invData->termsConditions)):
                    foreach($invData->termsConditions as $row):
                        echo '<tr>';
                            /* echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>'; */
                            echo '<td class=" fs-11"><ul><li> '.$row->condition.' </li></ul></td>';
                        echo '</tr>';
                    endforeach;
                endif;
            ?>
        </table>
        
        <table>
            <tr>
                <th colspan="2" style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;">
                    For, <?=$companyData->company_name?><br>
                </th>
            </tr>
            <tr>
                <td colspan="2" height="35"></td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align:bottom;text-align:right;font-size:1rem;padding:5px 2px;"><b>Authorised Signature</b></td>
            </tr>
        </table>

        <table class="table top-table" style="margin-top:10px; border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;font-size:12px;">This is computer generated Credit Note.</td>
            </tr>
        </table>
    </div>
</div>
