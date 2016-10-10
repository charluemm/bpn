$( document ).ready(function() 
{
	var source;
	var target;
	
	var listTable = $(".tournament-table").toArray();
	var table1 = $("#table-5 .list-group");
	var table2 = $("#table-6 .list-group");
	var table3 = $("#table-7 .list-group");
	var playerGrp1 = $("#player-pool-1 li.list-group-item").toArray();
	var playerGrp2 = $("#player-pool-2 li.list-group-item").toArray();
	var playerGrp3 = $("#player-pool-3 li.list-group-item").toArray();
	
	var timeout;
	var i = 1;
	
	$("#btn-run").click(function()
	{
		source = playerGrp1;
		target = $('#table-5 .list-group');
		timeout = setTimeout(function(){ selectRandomPlayer();}, 500);
	});
	
	$("#btn-stop").click(function(){
		clearTimeout(timeout);
	});

	$("#btn-refresh").click(function()
	{
		$listTable = $(".tournament-table");
		$listTable.each(function(){
			$(this).randomize();
			$(this).randomize();
			$(this).find('.list-group-item').each(function(i,obj){
				$(obj).html(" <span class=\"badge pull-left\">"+(i+1)+"</span>  "+$(obj).html());
			});
			$(this).sumPoints();
		});
		$(this).toggleClass('disabled');
	});
	
	
	function selectRandomPlayer()
	{
		// Markierung zurücksetzen
		$('.list-group-item').removeClass("list-group-item-danger");
		// Zufälligen Eintrag aus Liste wählen
		elemlength = source.length;
		randomnum = Math.floor(Math.random()*elemlength);
		randomitem = source[randomnum];

		// Element markieren
		$(randomitem).toggleClass("list-group-item-danger");

		// nach zufälligem Timeout wiederholen
		randTimeout = Math.floor(Math.random() * (600 - 200) + 200);
		timeout = setTimeout(function(){selectRandomPlayer()}, randTimeout);

		// max. Anzahl an Iterationen erreicht
		if((i++ % 6 == 0 && source.length != 0))
		{
			// Füge Element zur Zielliste hinzu
			$(target).append($(randomitem).removeClass('list-group-item-danger'));
			
			// nächsten Topf wählen
			// wenn 1
			if($(source).is($(playerGrp1)))
			{
				playerGrp1 = jQuery.grep(playerGrp1, function(value) {
					return value != randomitem;
				});
				source = playerGrp2;
			}
			// wenn Pot 2
			else if($(source).is($(playerGrp2)))
			{
				playerGrp2 = jQuery.grep(playerGrp2, function(value) {
					return value != randomitem;
				});
				source = playerGrp3;
			}
			// wenn Pot 3
			else if($(source).is($(playerGrp3)))
			{
				playerGrp3 = jQuery.grep(playerGrp3, function(value) {
					return value != randomitem;
				});
				source = playerGrp1;
			}
			// Fehler
			else
				alert("Unerwarteter Fehler: Zu viele Gruppen");
		}
		// keine Spieler in Auswahl
		else if(playerGrp1.length + playerGrp2.length + playerGrp3.length == 0)
		{
			timeout = clearTimeout(timeout);	
			i = 1;
		}
		// Ziel ist voll
		else if($(target).find('.list-group-item').length == $(target).data('max-player'))
		{
			// neues Ziel wählen
			if($(target).is($(table1)))
				target = table2;
			else if($(target).is($(table2)))
				target = table3;
			i = 1;
		}
		// restliche Spieler in Topf entsprechen max Spieler am Zieltisch
		else if(playerGrp1.length + playerGrp2.length + playerGrp3.length == $(target).data('max-player'))
		{
			// übrige Elemente hinzufügen
			$(target).append($(playerGrp1));
			$(target).append($(playerGrp2));
			$(target).append($(playerGrp3));
			// Variable zurücksetzen
			playerGrp1 = [];
			playerGrp2 = [];
			playerGrp3 = [];
			// Markierung entfernen
			$('.list-group-item').removeClass("list-group-item-danger");	
			// timeout stoppen
			timeout = clearTimeout(timeout);
			i = 1;
		}
	}
});

$.fn.sumPoints = function(){
	var sum = 0;
	$(this).find('.list-group-item').each(function(){
		sum += $(this).data('player-points');
	});
	$(this).closest('.panel').append('<div class=\"panel-footer\">Summe <span class="pull-right">' + sum + ' Punkte</span></div>');
	return this;
}

$.fn.randomize = function(selector){
    var $elems = selector ? $(this).find(selector) : $(this).children(),
        $parents = $elems.parent();

    $parents.each(function(){
        $(this).children(selector).sort(function(){
            return Math.round(Math.random()) - 0.5;
        }).detach().appendTo(this);
    });

    return this;
};