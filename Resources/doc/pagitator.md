Data tables compatibility
================================

$('#example').dataTable( {
                    "bProcessing": true,
                    "bServerSide": true,
                    headers: {
                            0: { sorter: false },
                            6: { sorter: false }
                    },
                    "sAjaxSource": "{{ path('route',{'_format':'json','_formatData':'dataTables'}) }}",
                    "fnServerData": function ( sSource, aoData, fnCallback ) {
                        $.getJSON( sSource, aoData, function (json) { 
                            var data = [];
                            json.aaData.forEach(function(myObj){
                                var array = $.map(myObj, function(value, index) {
                                    return [value];
                                });
                                data.push(array);
                            });
                            json.aaData = data;
                            fnCallback(json);
                        });
                    }
            } );