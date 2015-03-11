webshim.setOptions('forms-ext', {
    replaceUI: 'auto'
});
webshim.activeLang('de');
webshim.polyfill('forms forms-ext');

$(function(){
    $(".note-form-edit").click(function(){
        var rowId = "#tudNoteFormRow" + $(this).data("tud-id");
        if($(rowId).css("display") === "none"){
            $(this).html("Hide Note");
            $(rowId).css("display", "table-row");
            //Fix webshim styles
            $(rowId + " .ws-date," +rowId + " .date-input-buttons").attr("style", "");
        } else {
            $(this).html("Show Note");
            $(rowId).css("display", "none");
        }
    });

    $(".tud-note-submit").click(function(e){
        var data, form = $(this).parents("form").first();
        data = form.serialize();
        $.ajax({
            type: "GET",
            url: "/service.php/MyCheckouts/Service/SetNote",
            data: data,
            cache: false,
            beforeSend: function(){
                var state = form.find(".tud-ajax-state");
                state.html("Saving...");
                state.css("color", "#000");
            }
        }).done(function(){
            var state = form.find(".tud-ajax-state");
            state.html("Saved");
            state.css("color", "#008800");
            setTimeout(function() { state.html(); }, 5000)
        }).fail(function(){
            var state = form.find(".tud-ajax-state");
            state.html("Save failed");
            state.css("color", "#cf0000");
        });

        e.preventDefault();
        return false;
    });

    $(".tud-note-form input, .tud-note-form textarea").change(function(){
        $(this).parents(".tud-note-form").first().find(".tud-ajax-state").html("");
    })
});