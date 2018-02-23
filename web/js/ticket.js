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

        var $cart = $('#selected-seats'),
            $counter = $('#counter'),
            $total = $('#total'),
            sc = $('#ticket-map').seatCharts({
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
                        //let's create a new <li> which we'll add to the cart items
                        // $('<li>'+this.data().category+' Seat # '+this.settings.label+': <b>$'+this.data().price+'</b> <a href="#" class="cancel-cart-item">[cancel]</a></li>')

                        create_row(this.settings.id);

                        /*$('<li>'+this.data().category+': <b>'+this.settings.id+'</b></li>')
                            .attr('id', 'cart-item-'+this.settings.id)
                            .data('seatId', this.settings.id)
                            .appendTo($cart);
                        $counter.text(sc.find('selected').length+1);
                        $total.text(recalculateTotal(sc)+this.data().price);*/
                        return 'selected';
                    } else if (this.status() == 'selected') {
                        //update the counter
                        $counter.text(sc.find('selected').length-1);
                        //and total
                        $total.text(recalculateTotal(sc)-this.data().price);
                        //remove the item from our cart
                        $('#cart-item-'+this.settings.id).remove();
                        //seat has been vacated
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

function recalculateTotal(sc) {
    var total = 0;
    sc.find('selected').each(function () {
        total += this.data().price;
    });
    return total;
}

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

function create_row(item){
    var $cart = $('#tickets tbody');
    $('<tr><td>'+item.replace("_", "/")+'</td><td><select class="form-control"><option>Besplatna</option><option>Pojedinačna</option></select></td><td></td></tr>')
        .attr('id', 'cart-item-'+item)
        .data('seatId', item)
        .appendTo($cart);
}
