var firstSeatLabel = 1;

function get_seats(sc) {
    var retval = [];
    sc.find('selected').each(function () {
        var obj = $(this);
        retval.push(obj[0].settings.id);
    });
    return retval;
}

function load_seats(sc) {
    var $loader = new ajaxLoader($('.wrapper'));
    var entityId = $performance_id; // $('#reservation_id').val();
    var url = Routing.generate('list_seats', {'id': entityId});
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: url,
        success: function (data) {
            var $reserve = [];
            var $sold = [];
            data.forEach(function(item) {
                if(item.status){
                    $sold.push(item.seat);
                } else {
                    $reserve.push(item.seat);
                }
            });
            sc.get($reserve).status('unavailable reserve');
            sc.get($sold).status('unavailable');
            $loader.remove();
        }
    })
}