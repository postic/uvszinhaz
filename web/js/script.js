var firstSeatLabel = 1;

$(document).ready(function() {

    if($('#seat-map').length > 0){
        var $cart = $('#selected-seats'),
            $counter = $('#counter'),
            $total = $('#total'),
            sc = $('#seat-map').seatCharts({
                map: [
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                    'fffffffffffffffff',
                ],
                seats: {
                    f: {
                        price   : 100,
                        classes : 'first-class', //your custom CSS class
                        category: 'Available'
                    }

                },
                naming : {
                    top : false,
                    getLabel : function (character, row, column) {
                        return firstSeatLabel++;
                    },
                },
                legend : {
                    node : $('#legend'),
                    items : [
                        [ 'f', 'available',   'Available' ],
                        //[ 'e', 'available',   'Economy Class'],
                        [ 'f', 'unavailable', 'Already Booked']
                    ]
                },
                click: function () {
                    if (this.status() == 'available') {
                        //let's create a new <li> which we'll add to the cart items
                        // $('<li>'+this.data().category+' Seat # '+this.settings.label+': <b>$'+this.data().price+'</b> <a href="#" class="cancel-cart-item">[cancel]</a></li>')
                        $('<li>'+this.data().category+': <b>'+this.data().price+'&nbsp;RSD</b> <a href="#" class="cancel-cart-item">[cancel]</a></li>')
                            .attr('id', 'cart-item-'+this.settings.id)
                            .data('seatId', this.settings.id)
                            .appendTo($cart);
                        $counter.text(sc.find('selected').length+1);
                        $total.text(recalculateTotal(sc)+this.data().price);
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

        //this will handle "[cancel]" link clicks
        $('#selected-seats').on('click', '.cancel-cart-item', function () {
            //let's just trigger Click event on the appropriate seat, so we don't have to repeat the logic here
            sc.get($(this).parents('li:first').data('seatId')).click();
        });

        //let's pretend some seats have already been booked
        //var data = load_seats();
        //alert(data);
        //sc.get(data).status('unavailable');
        load_seats(sc);

        $('.checkout-button').on('click', function () {
            get_seats(sc);
        });
    }

    $('.delete-btn').on('click', function () {
        var entityId = $(this).attr('data-entity-id');
        $('.remove_item').attr('data-entity-id', entityId);
    });

    $('.remove_item').click(function () {
        var entityId = $(this).attr('data-entity-id');
        var url = Routing.generate('delete_reservation', {'id': entityId});
        window.location.href = url;
    });



    // delete seat
    $('.delete-btn-seat').on('click', function () {
        var entityId = $(this).attr('data-entity-id');
        $('.remove-seat').attr('data-entity-id', entityId);
    });

    $('.remove-seat').click(function () {
        var entityId = $(this).attr('data-entity-id');
        var url = Routing.generate('delete_seat', {'id': entityId});
        window.location.href = url;
    });



    // snimanje sedista u bazu
    $('.checkout-button').click(function () {
        var url = Routing.generate('add_seat');
        var seats = get_seats(sc);
        var data = new Object();
        data.id = $('#reservation_id').val();
        data.seats = seats;
        $.ajax({
            type: 'POST',
            data: JSON.stringify(data),
            dataType: 'json',
            url: url,
            success: function (entityId) {
                var url = Routing.generate('show_reservation', {'id': entityId});
                window.location.href = url;
            }
        })
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

    $('#modal-info').modal('show');

    var entityId = $('#reservation_id').val();
    var url = Routing.generate('list_seats', {'id': entityId});
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: url,
        success: function (data) {
            sc.get(data).status('unavailable');
            $('#modal-info').modal('hide');
        }
    })
}
