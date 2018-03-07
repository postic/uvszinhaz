var firstSeatLabel = 1;

$(document).ready(function() {

    if($('#ticket-map').length > 0 && $performance['scena'] == 1){

        // mreza za veliku scenu
        var $map = [
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            '_ffffffffffffffffff_',
            'ffffffffffffffffffff',
            '_bbbbbbbbbbbbbbbbbb_',
            '_bbbbbbbbbbbbbbbbbb_'
        ];

        var sc = $('#ticket-map').seatCharts({
            map: $map,
            seats: {
                f: {
                    classes : 'first-class' //your custom CSS class
                },
                b: {
                    classes : 'balcony-class' //your custom CSS class
                }
            },
            naming : {
                top : false,
                left : true,
                right : true,
                getId  : function(character, row, column) {
                    if(row != 11 ) {
                        column = parseInt(column)-1;
                        return row + '_' + column.toString();
                    }
                    else return row + '_' + column;
                },
                getLabel : function (character, row, column) {
                    if(row != 11 ) {
                        column = parseInt(column)-1;
                        return column.toString();
                    }
                    else return column;
                }
            },
            legend : {
                node : $('#legend'),
                items : [
                    [ 'f', 'available',   'Slobodna sedišta' ],
                    [ 'f', 'unavailable', 'Zauzeta sedišta']
                ]
            },
            click: function () {
                if (this.status() == 'available') {
                    create_row_ticket(this.settings.id);

                    // Izracunavanje sume
                    var $sum = 0;
                    $('.cart-item-price').each(function() {
                        $sum = $sum + parseInt($(this).text());
                    });
                    $('#sum').text($sum);

                    return 'selected';
                } else if (this.status() == 'selected') {
                    //remove the item from our cart
                    $('#cart-item-'+this.settings.id).remove();

                    // Izracunavanje sume
                    var $sum = 0;
                    $('.cart-item-price').each(function() {
                        $sum = $sum + parseInt($(this).text());
                    });
                    $('#sum').text($sum);

                    return 'available';
                } else if (this.status() == 'unavailable') {
                    //seat has been already booked
                    return 'unavailable';
                } else {
                    return this.style();
                }
            }
        });
        load_seats(sc);
    }

    // brisanje sedišta
    $('.delete-button-seat').on('click', function () {
        var entityId = $(this).attr('data-entity-id');
        $('.delete-seat').attr('data-entity-id', entityId);
    });

    $('.delete-seat').click(function () {
        var entityId = $(this).attr('data-entity-id');
        var url = Routing.generate('remove_seat', {'id': entityId});
        window.location.href = url;
    });

});

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
    var entityId = $performance.ID; // $('#reservation_id').val();
    var url = Routing.generate('list_seats', {'id': entityId});
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: url,
        success: function (data) {
            sc.get(data).status('unavailable');
            $loader.remove();
        }
    })
}

function create_row_ticket($data){

    var $cart = $('#tickets tbody');
    // add row
    $cart
        .append($('<tr>')
            .attr('id', 'cart-item-'+$data)
            .data('seatId', $data)
            .append($('<td>')
                .append($data.replace("_", "/")))
            .append($('<td>')
                .append($('<select>')
                    .attr('id', 'cart-item-type-'+$data)
                    .addClass('form-control')))
            .append($('<td>')
                .append($('<span>')
                    .attr('id', 'cart-item-price-'+$data)
                    .addClass('cart-item-price')
                    .append($performance.cena[1]))));

    $.each($performance.cena, function(key, value) {

        var $text;
        switch(key) {
            case '1':
                $text = 'Pojedinačna';
                break;
            case '2':
                $text = 'Grupna';
                break;
            case '3':
                $text = 'Studentska';
                break;
            case '4':
                $text = 'Penzionerska';
                break;
            case '5':
                $text = 'Besplatna';
                break;
            case '6':
                $text = 'Stručna';
                break;
        }

        $('#cart-item-type-'+$data)
            .append($('<option></option>')
                .attr('value',key)
                .attr('data-entity-id',value)
                .text($text));
    });
    $('#cart-item-type-'+$data).val(1);

    // Izracunavanje sume
    $('#cart-item-type-'+$data).change(function() {
        $sum = 0;
        var $price = $('option:selected', this).attr('data-entity-id');
        $('#cart-item-price-'+$data).text($price);
        $('.cart-item-price').each(function() {
            $sum = $sum + parseInt($(this).text());
        });
        $('#sum').text($sum);
    });

    // Izracunavanje sume
    var $sum = 0;
    $('.cart-item-price').each(function() {
        $sum = $sum + parseInt($(this).text());
    });
    $('#sum').text($sum);
}
