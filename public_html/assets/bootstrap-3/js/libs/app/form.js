$('form.ajax_form').on('submit', function(e) {
        e.preventDefault();
        
        //Set up vars
        var that = $(this),
        url = that.attr('action'),
        type = that.attr('method'),
        alertClass = that.attr('alert-class');  //This tells us what alert to show when modal closes
        
/* Tellenis 15/12/13 */
        indRow =  that.closest('tr').attr('id'); //Get the id of the closest <tr>?


        tableId = that.attr('table-id'); // the id of the current table


/* Tellenis 15/12/13 */
        var row = that.parents( 'tr' );
        
        //var indRow = $(this).index(); // this worked
        var indRow = parseInt($( "#rowIndex" ).html());


    
        //Is it a modal?
        modal = '';
        if( that.hasClass('modal_form') ) {
            modal = 'modal';
        }
        
        //Serialise the data to allow for radio/checkboxes
        data = that.serialize();
        console.log('data:', data);

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function(response) {
                //response is an array of the updated/created record
                response_parsed = $.parseJSON(response);
                console.log(response);

                for(var key in response) {

                    // value is empty string
                    if(response[key] === 'id') {
                        delete response[key];
                    }
                }
                
                //if update/insert fails, then we see this message
                if ( response_parsed.message == '[uhoh]' ){
                    //console.log('theres an error');
                    //
                    //Show some errors
                    if ( modal == 'modal' ) {
                        $('.modal-fail').removeClass('hide');
                    }
                    else {
                       $('.form-fail').removeClass('hide');
                    }
                }

                //Else... woo hoo! Itw worked...
                else {
                    if ( modal == 'modal' ) {
                        $('#modal').modal('hide');
                        tableId = 'task-table';
                        //Do we have a table to redraw?
                        if ( tableId ) {
                            
                            //We need to remove unwanted index of response_parsed
                            //(This is so it just just shows the data currently displayed in this table)

                            var data_rows;
                            var rowArray =[];
                            var cols = $('#'+tableId).attr('data-cols');
                             delete response_parsed['message'];
                             $.each(response_parsed['q'], function( c, v ) {           
                                var rem;
                                if (jQuery.inArray(c, cols.replace(/,\s+/g, ',').split(',')) < 0) {
                                    rem=c;                      
                                    delete response_parsed['q'][rem];
                                }
                                else{
                                    rowArray.push(response_parsed['q'][c]);
                                // data_rows = ["6382","gggg","0"]; // format accepted by datatable for fnAddData
                                    }
                             });
                        
                            data_rows=rowArray;
                            //console.log("RES-"+json.q.row);
                            var dTable = $('#'+tableId).dataTable({ bRetrieve : true });    
                            
                            if(indRow>=0)
                            dTable.fnUpdate(data_rows,indRow);
                            else
                            dTable.fnAddData(data_rows);



                            //get the cols for this table - DOESN'T WORK!
                            // var cols = $('#'+tableId).attr('cols');
                            // console.log('cols=', cols);

                            //Remove all the unwanted indices
                            //$.each(cols, function( c, v ) {
                            //     rowArray.push(json.q[v]);
                            // });
                            
                            //Now we've got an object with the right strcutrue, pass it to fnAddData() and fnRedraw()?

                        }
                    }

                    //Now show some success messages
                    $('.form-fail').addClass('hide');   //Just in case we had an error before
                    $('.form-success.alert-'+alertClass).removeClass('hide');
                    window.setTimeout(function() {
                        $('.form-success.alert-'+alertClass).addClass('hide');
                    }, 1500);
                }
            }
        });

        return false;
    });
