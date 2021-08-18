(function ($) {
    $(document).ready(function () {
        if($('input[name=merchant_id]').length){
            let rows = 0;
            let merchantIdObject;
            let availableCountries = pledgAvailableCountries;
            let merchant_id_input = $('input[name=merchant_id]');
            try{
                merchantIdObject = JSON.parse($(merchant_id_input).val());
            } catch {
                merchantIdObject = {
                    default: $(merchant_id_input).val()
                };
            }
            let container = $(merchant_id_input).parent();
            $(merchant_id_input).addClass('hidden');
            
            $(container).prepend('<a class="btn btn-info js-add-row">Add a Merchant Id Mapping</a>');
            $(container).find('a.js-add-row').on('click', function(){
                addRow("", "");
                rows++;
            });

            let html = '<table class="table" id="merchantIdAdder">';
                html += '<thead>';
                    html += '<tr>';
                    html += '<th>Country</th>';
                    html += '<th>Merchant Id</th>';
                    html += '<th>Action</th>';
                    html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                html += '</tbody>';
            html += '</table>';
            $(container).prepend(html);
            let merchant_id_adder_body = $('#merchantIdAdder tbody');
            for (const [country, merchantId] of Object.entries(merchantIdObject)) {
                if(typeof availableCountries[country] !== 'undefined'){
                    addRow(country, merchantId.replace(/"/g, "&quot;"));
                    rows++;
                }
            }
            if(rows === 0){
                addRow("", "");
                rows++;
            }

            function addRow(country, merchantId){
                let r = rows;
                let html = '';
                html += '<tr id="row'+r+'" class="bodyRow">';
                html += '<th>';
                    // Country
                    html += '<select class="form-select" id="select'+r+'">';
                    for(const [c, v] of Object.entries(availableCountries)){
                        if(c === country){
                            html += '<option value="' + c + '" selected>' + v + '</option>';
                        } else {
                            html += '<option value="' + c + '">' + v + '</option>';
                        }
                    }
                    html += '</select>';
                html += '</th>';
                html += '<th>';
                    // MerchantId
                    html += '<input value="' + merchantId + '" type="text" class="form-control" id="merchantId'+r+'"/>';
                html += '</th>';
                html += '<th>';
                    // Remove button
                    html += '<a class="btn btn-info"><i class="icon-trash"></i></a>';
                html += '</th>';
                html += '</tr>';
                $(merchant_id_adder_body).append(html);
                $(merchant_id_adder_body).find('#row'+r+' .btn').on('click', function(){removeRow(r)});
                $(merchant_id_adder_body).find('#row'+r+' input').on('input', function(){updateMerchantIdInput()});
                $(merchant_id_adder_body).find('#row'+r+' select').on('change', function(){updateMerchantIdInput()});
                updateMerchantIdInput();
            }
        
            function removeRow(rowNb){
                if($(merchant_id_adder_body).find('tr.bodyRow').length>1){
                    let row = $(merchant_id_adder_body).find('tr#row'+rowNb);
                    if(row.length){
                        $(row).first().remove();
                    }
                    updateMerchantIdInput();
                }
            }
    
            function updateMerchantIdInput(){
                let r = $(merchant_id_adder_body).find('tr.bodyRow').toArray();
                let Cs = [];
                r.forEach(el => {
                    let country = $(el).find('.form-select').val();
                    Cs.push(country);
                    let merchantId = $(el).find('input').val();
                    if(merchantId !== ""){
                        merchantIdObject[country] = merchantId;
                    }
                });
                for(const [c, v] of Object.entries(merchantIdObject)){
                    if(!Cs.includes(c) && c !== 'default'){
                        delete merchantIdObject[c];
                    }
                }
                $(merchant_id_input).val(JSON.stringify(merchantIdObject));
            }
        }
    });
  })(jQuery);
  