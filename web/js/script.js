var firstSeatLabel = 1;

$(document).ready(function() {

    if($('#seat-map').length > 0 && $('#scena').val() == 1){

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
            sc = $('#seat-map').seatCharts({
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
                    /*
                    getLabel : function (character, row, column) {
                        return null; // firstSeatLabel++;
                    },
                    */
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
                        console.info(this);
                        //let's create a new <li> which we'll add to the cart items
                        // $('<li>'+this.data().category+' Seat # '+this.settings.label+': <b>$'+this.data().price+'</b> <a href="#" class="cancel-cart-item">[cancel]</a></li>')
                        $('<li>'+this.data().category+': <b>'+this.settings.id+'</b></li>')
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

        load_seats(sc);
    }

    // brisanje rezervacije
    $('.delete-reservation-btn').on('click', function () {
        var entityId = $(this).attr('data-entity-id');
        $('.delete-reservation').attr('data-entity-id', entityId);
    });

    $('.delete-reservation').click(function () {
        var entityId = $(this).attr('data-entity-id');
        var url = Routing.generate('delete_reservation', {'id': entityId});
        window.location.href = url;
    });

    // otkazivanje rezervacije
    $('.cancel-reservation-btn').on('click', function () {
        var entityId = $(this).attr('data-entity-id');
        $('.cancel-reservation').attr('data-entity-id', entityId);
    });

    $('.cancel-reservation').click(function () {
        var entityId = $(this).attr('data-entity-id');
        var url = Routing.generate('cancel_reservation', {'id': entityId});
        window.location.href = url;
    });

    // brisanje sedišta
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
    $('#checkout-button').click(function () {
        var url = Routing.generate('add_seat');
        var $seats = get_seats(sc);
        var $tip_karte = $('#tip_karte').val();
        var data = new Object();
        data.id = $('#reservation_id').val();
        data.seats = $seats;
        data.tip_karte = $tip_karte;
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

    // zatvaranje rezervacije
    $('#close-reservation-button').on('click', function () {
        var reservationId = $(this).attr('data-entity-id');
        $('#close_reservation_button').attr('data-entity-id', reservationId);
    });

    $('#close_reservation_button').click(function () {
        var reservationId = $(this).attr('data-entity-id');
        var url = Routing.generate('close_reservation', {'id': reservationId});
        window.location.href = url;
    });

    // prikazivanje rezervacije
    $('.display-reservation-btn').click(function (e) {
        e.preventDefault();
        var $loader = new ajaxLoader($('.wrapper'));
        var $reservationId = $(this).attr('data-entity-id');
        var url = Routing.generate('get_reservation', {'id': $reservationId});
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            success: function (data) {
                $loader.remove();
                var $modal = '';
                switch (data.p_status_id) {
                    case 1:
                        $modal = 'modal modal-info fade';
                        break;
                    case 2:
                        $modal = 'modal modal-success fade';
                        break;
                    case 3:
                        $modal = 'modal modal-danger fade';
                        break;
                }
                $('#display-reservation-modal').removeClass().addClass($modal);
                $('.form-group').hide();
                $.each(data, function(key,  value) {
                    $('#'+key).show();
                    $('#'+key+' .data').text(value);
                })
                $('#display-reservation-modal').modal('show');
            }
        });
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
    var entityId = $('#performance_id').val(); // $('#reservation_id').val();
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


// Reservation form
$(document).ready(function() {

    var $performance = $('#salexuserbundle_reservation_performanceId');

    $performance.change(function() {

        $('.help').hide();
        $('#salexuserbundle_reservation_brojGrupne').hide();
        $('#salexuserbundle_reservation_brojStudentske').hide();
        $('#salexuserbundle_reservation_brojPenzionerske').hide();
        $('#salexuserbundle_reservation_brojPojedinacne').hide();
        $('#salexuserbundle_reservation_brojBesplatne').hide();

        var $loader = new ajaxLoader($('.wrapper'));

        var $performanceId = $performance.val();
        var url = Routing.generate('get_performance', {'id': $performanceId});
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            success: function (data) {
                var $cene = data[0].cena;
                var $scena = data[0].scena;
                $('#salexuserbundle_reservation_scena').val($scena);
                for( var $i in $cene ) {
                    switch($i) {
                        case '1':
                            $('#salexuserbundle_reservation_brojPojedinacne').show();
                            $('#salexuserbundle_reservation_brojPojedinacne').after( "<span class='help'>Cena jedne karte: "+ parseFloat($cene[$i]).toFixed(2).replace(".", ",") +"</span>" );
                            break;
                        case '2':
                            $('#salexuserbundle_reservation_brojGrupne').show();
                            $('#salexuserbundle_reservation_brojGrupne').after( "<span class='help'>Cena jedne karte: "+ parseFloat($cene[$i]).toFixed(2).replace(".", ",") +"</span>" );
                            break;
                        case '3':
                            $('#salexuserbundle_reservation_brojStudentske').show();
                            $('#salexuserbundle_reservation_brojStudentske').after( "<span class='help'>Cena jedne karte: "+ parseFloat($cene[$i]).toFixed(2).replace(".", ",") +"</span>" );
                            break;
                        case '4':
                            $('#salexuserbundle_reservation_brojPenzionerske').show();
                            $('#salexuserbundle_reservation_brojPenzionerske').after( "<span class='help'>Cena jedne karte: "+ parseFloat($cene[$i]).toFixed(2).replace(".", ",") +"</span>" );
                            break;
                        case '5':
                            $('#salexuserbundle_reservation_brojBesplatne').show();
                            $('#salexuserbundle_reservation_brojBesplatne').after( "<span class='help'>Besplatna karta</span>" );
                            break;
                    }
                }
                $loader.remove();
            }
        })
    });
});