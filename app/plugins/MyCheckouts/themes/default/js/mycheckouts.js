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
                form.parents("td").first().css("background", "#fefec0");
                state.html("Speichere Änderungen...");
            }
        }).done(function(){
            var state = form.find(".tud-ajax-state");
            form.parents("td").first().css("background", "#d5ffd5");
            state.html("Änderungen gespeichert.");
        }).fail(function(){
            var state = form.find(".tud-ajax-state");
            form.parents("td").first().css("background", "#ffaeae");
            state.html("Speichern fehlgeschlagen.");
        });

        e.preventDefault();
        return false;
    });

    $(".tud-note-form input, .tud-note-form textarea").change(function(){
        $(this).parents("td").first().css("background", "#fefec0");
        $(this).parents(".tud-note-form").first().find(".tud-ajax-state").html("Ungespeicherte Änderungen vorhanden.");
    })
});