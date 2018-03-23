$( document ).ready(function() 
{
	
	var listTable = $(".tournament-table").toArray();
	var source;
	var target;
	var timeout;
	
	var playerGrp1 = $("#player-pool-1 li.list-group-item").toArray();
	var playerGrp2 = $("#player-pool-2 li.list-group-item").toArray();
	var playerGrp3 = $("#player-pool-3 li.list-group-item").toArray();
	
	var i = 1;
	
	// click button run
	$("#btn-run").click(function(){
		startDraw();
	});
	// click button stop
	$("#btn-stop").click(function(){
		stopDraw();
	});
	// click button refresh
	$("#btn-refresh").click(function()
	{
		randomizeTablePlayer();
		$(this).toggleClass('disabled');
	});
	// click button save
	$('#btn-save').click(function(){
		saveTableSeats();
	});
	
	// ping dash button
	(function button_start_draw() {
        $.ajax({
            url: dashButtonStatusUrl,
            success: function(data){
                console.log(data);
                if(data){
                    $(".navbar-signal").hide();
                   console.log("ping successfull");
                   // start light
                   $.ajax({
                       url: dmxDrawStartUrl,
                       success: function(){
                           // start audio
                           $('#audio_draw_start').trigger("play").delay(2500);
                           $('#audio_draw_running').trigger("play");
                           startDraw();
                       }
                   });
                }
                else{
                    //console.log('retry ping');
                    setTimeout(button_start_draw, 500);
                }
                
            },
            error: function(err){
                console.error(err);
            }
        });
    })();
	
	// start draw
	function startDraw()
	{
		source = playerGrp1;
		target = 0;
		timeout = setTimeout(function(){ selectRandomPlayer();}, 500);		
	}
	
	
	function stopDraw()
	{
		timeout = clearTimeout(timeout);
		// start light
        $.ajax({
            url: dmxDrawStopUrl,
            success: function(){
                // start audio
                $('#audio_draw_running').trigger("pause");
                startDraw();
            }
        });
        
	}
	
	function randomizeTablePlayer()
	{
		$(listTable).each(function(){
			$(this).randomize();
			$(this).randomize();
			$(this).find('.list-group-item').each(function(i,obj){
				$(obj).html(" <span class=\"badge pull-left\">"+(i+1)+"</span>  "+$(obj).html());
			});
			$(this).sumPoints();
		});		
	}
	
	function saveTableSeats()
	{
		$(listTable).each(function(){
			$(this).saveSeats();
		});		
	}
	
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
		if((i++ % 11 == 0 && source.length != 0))
		{
			// Füge Element zur Zielliste hinzu
			//$(listTable[target]).append($(randomitem).removeClass('list-group-item-danger'));
			$(listTable[target]).append($(randomitem).removeClass('list-group-item-danger'));
			
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
		else if($(listTable[target]).find('.list-group-item').length == $(listTable[target]).data('max-player'))
		{
			// neues Ziel wählen
			if(target < listTable.length)
				target++;
			else
				target = 0;
			
			i = 1;
			return ;
			
			if($(listTable[target]).is($(table1)))
				target = table2;
			else if($(listTable[target]).is($(table2)))
				target = table3;
			i = 1;
		}
		// restliche Spieler in Topf entsprechen max Spieler am Zieltisch
		else if(playerGrp1.length + playerGrp2.length + playerGrp3.length <= ($(listTable[target]).data('max-player') - $(listTable[target]).find('.list-group-item').length))
		{
			// übrige Elemente hinzufügen
			$(listTable[target]).append($(playerGrp1));
			$(listTable[target]).append($(playerGrp2));
			$(listTable[target]).append($(playerGrp3));
			// Variable zurücksetzen
			playerGrp1 = [];
			playerGrp2 = [];
			playerGrp3 = [];
			// Markierung entfernen
			$('.list-group-item').removeClass("list-group-item-danger");	
			// timeout stoppen
			timeout = clearTimeout(timeout);
			i = 1;
			stopDraw();
		}
	}
});

$.fn.saveSeats = function(){
	var list = [];
	var $this = $(this);
	
	$this.find('.list-group-item.seat-item').each(function(){
		list.push($(this).data('player-id'));
	});

	$.ajax({
		url: ajaxTableSeatUrl,
		data: { 
			'table': $this.data('table-id'),
			'list': list 
		},
		type: 'POST',
		success: function(data){
			$this.closest('.panel').removeClass('panel-default panel-danger').addClass('panel-success');
		},
		error: function(data){
			$this.closest('.panel').removeClass('panel-default panel-success').addClass('panel-danger');
			console.debug(data);
		}
	});
	return this;
}

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