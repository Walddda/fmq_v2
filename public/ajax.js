
$(document).ready(function(){

    $('.deleteBtn').click(function (e) {
        e.preventDefault();
    
        itemId = $(this).attr('id');
    
        $.ajax({
    
            url: '/del/Quote',
            data: {'entityId':itemId},
            method: 'post',
            success: function (data, reponse) {
    
             if(data == 'good' ){
                //appear pop to say success blabla
                console.log('gooog');
                location.reload();
             }
            },
            error: function () {
                //appear pop to say error blabla
                console.log('baaab');
            },
        });
    
    });

    $('.deleteBtnM').click(function (e) {
        e.preventDefault();
    
        itemId = $(this).attr('id');
    
        $.ajax({
    
            url: '/del/Movie',
            data: {'entityId':itemId},
            method: 'post',
            success: function (data, reponse) {
    
             if(data == 'good' ){
                //appear pop to say success blabla
                console.log('gooog');
                location.reload();
             }
            },
            error: function () {
                //appear pop to say error blabla
                console.log('baaab');
            },
        });
    });

    $("#editForm").submit(function (e) {
        console.log($(this).serialize())
        e.preventDefault();    
        $.ajax({
            url: '/editSub',
            data: $(this).serialize(),
            method: 'post',
            success: function (data, reponse) {
                console.log(data)
            },
            error: function () {
            },
        });
    });
})