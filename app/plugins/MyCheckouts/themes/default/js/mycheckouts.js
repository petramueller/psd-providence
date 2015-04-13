webshim.setOptions('forms-ext', {
    replaceUI: 'auto'
});
webshim.activeLang('de');
webshim.polyfill('forms forms-ext');

$(function(){
    $(".note-form-edit").click(function(){
        var rowId = "#tudNoteFormRow" + $(this).data("tud-id");
        if($(rowId).css("display") === "none"){
            $(this).html($("#tudCheckoutList").data("hide-note"));
            $(rowId).css("display", "table-row");
            //Fix webshim styles
            $(rowId + " .ws-date," +rowId + " .date-input-buttons").attr("style", "");
        } else {
            $(this).html($("#tudCheckoutList").data("show-note"));
            $(rowId).css("display", "none");
        }
    });

    $(".tud-note-submit").click(function(e){
        var data, form = $(this).parents("form").first(), servicePath = tudUrlRoot + "/service.php/MyCheckouts/Service/SetNote";
        data = form.serialize();
        $.ajax({
            type: "GET",
            url: servicePath,
            data: data,
            cache: false,
            beforeSend: function(){
                var state = form.find(".tud-ajax-state");
                form.parents("td").first().css("background", "#fefec0");
                state.html($("#tudCheckoutList").data("saving-changes"));
            }
        }).done(function(){
            var state = form.find(".tud-ajax-state");
            form.parents("td").first().css("background", "#d5ffd5");
            state.html($("#tudCheckoutList").data("changes-saved"));
        }).fail(function(){
            var state = form.find(".tud-ajax-state");
            form.parents("td").first().css("background", "#ffaeae");
            state.html($("#tudCheckoutList").data("saving-failed"));
        });

        e.preventDefault();
        return false;
    });

    $(".tud-note-form input, .tud-note-form textarea").change(function(){
        $(this).parents("td").first().css("background", "#fefec0");
        $(this).parents(".tud-note-form").first().find(".tud-ajax-state").html($("#tudCheckoutList").data("unsaved-changes"));
    })
});