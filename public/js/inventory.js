$(document).ready(function() {
    var inventory_products_table = $('#inventory_products_table').DataTable({
        // processing: true,
         serverSide: true,
        ajax: {
            url: '/inventory/' + $('#inventory_id').val(),
            data: function (d) {
                d.zeroQuantity = $('#zeroQuantity').is(':checked');
                d.expired = $('#expired').is(':checked');
            }
        },
        "fnDrawCallback": function (oSettings) {
            sumInventoryTableAmount($('#inventory_products_table'));
            __currency_convert_recursively($('#inventory_products_table'));
        }
    });

    $('#zeroQuantity, #expired').on('change', function () {
        inventory_products_table.ajax.reload();
    });
});

function sumInventoryTableAmount(element){
    
    var inventoryCost = 0;
    element.find('span.input-number').each(function(){
        inventoryCost += (parseFloat($(this).text()) > 0)?parseFloat($(this).text()):0;
    });
    element.find('.inventoryTotalCost').text('$ '+inventoryCost.toFixed(2));
 
}