$( document ).ready(function() 
{
	var source;
	var target;
	
	var listTable = $(".tournament-table").toArray();
	var table1 = $("#table-5 .list-group");
	var table2 = $("#table-6 .list-group");
	var table3 = $("#table-7 .list-group");
	var playerGrp1 = $("#player-pool-1 .list-group-item").toArray();
	var playerGrp2 = $("#player-pool-2 .list-group-item").toArray();
	var playerGrp3 = $("#player-pool-3 .list-group-item").toArray();
	var lastSelect = 0;
	var timeout;
	var i = 1;
	var j = 1;
	
	var selected_seat = null;
	
	$("#btn-run").click(function()
	{
		source = playerGrp1;
		target = $('#table-5 .list-group');
		timeout = setTimeout(function(){ selectRandomPlayer();}, 500);
	});
	
	$("#btn-refresh").click(function()
	{
		$(table1).randomize();
		$(table1).randomize();
		$(table1).find('.list-group-item').each(function(i,obj){
			$(obj).html(" <small>Platz "+(i+1)+"</small>  "+$(obj).html());
		});

		$(table2).randomize();
		$(table2).randomize();
		$(table2).find('.list-group-item').each(function(i,obj){
			$(obj).html(" <small>Platz "+(i+1)+"</small>  "+$(obj).html());
		});
		
		$(table3).randomize();
		$(table4).randomize();
		$(table5).find('.list-group-item').each(function(i,obj){
			$(obj).html(" <small>Platz "+(i+1)+"</small>  "+$(obj).html());
		});
	});
	
	$("#btn-stop").click(function(){
		clearTimeout(timeout);
	});

	function run()
	{
		i = 1;
		// Pot 1
		source = playerGrp1;
//		selectRandomPlayer();
		
		// Durchlaufe Tische
		$(listTable).each(function(i, obj)
		{
			var listSeats = $(obj).find('.poker-seat');
			// Durchlaufe sitzplätze (zufällig)
			$(listSeats).each(function(i, obj)
			{
				$(listSeats).removeClass('active')
				$(obj).addClass('active').wait();
				selectRandomPlayer();
				// finde für jeden sitzplatz zufällig einen spieler aus topf
			});
		});
	}
	
	function selectRandomPlayer()
	{
		// Zufälligen Eintrag aus Liste wählen
		$('.list-group-item').removeClass("list-group-item-danger");
		elemlength = source.length;
		randomnum = Math.floor(Math.random()*elemlength);
		randomitem = source[randomnum];
		randTimeout = Math.floor(Math.random() * (600 - 200) + 200);
		
		console.debug(elemlength);
		$(randomitem).toggleClass("list-group-item-danger");
		timeout = setTimeout(function(){selectRandomPlayer()}, randTimeout);

		if(playerGrp1.length + playerGrp2.length + playerGrp3.length == 0 || $(target).find('.list-group-item').length == 9)
		{
			$(target).randomize();
			if($(target).not(table1).length === 0)
				target = table2;
			else if($(target).not(table2).length === 0)
				target = table3;
			else if($(target).not(table3).length === 0)
			{
				timeout = clearTimeout(timeout);
			}
			i = 1;
		}
		else if((i++ % 16 == 0 && source.length != 0) || source.length == 1)
		{
			console.debug($(randomitem));
			console.debug("Auswahl: "+$(randomitem).text());
			$(target).append($(randomitem).removeClass('list-group-item-danger'));

			// nächsten Topf wählen
			// wenn 1
			if($(source).not(playerGrp1).length === 0)
			{
				playerGrp1 = jQuery.grep(playerGrp1, function(value) {
					return value != randomitem;
				});
				source = playerGrp2;
			}
			// wenn Pot 2
			else if($(source).not(playerGrp2).length === 0)
			{
				playerGrp2 = jQuery.grep(playerGrp2, function(value) {
					return value != randomitem;
				});
				source = playerGrp3;
			}
			// wenn Pot 3
			else if($(source).not(playerGrp3).length === 0)
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
	}
});

$.fn.randomize = function(selector){
    var $elems = selector ? $(this).find(selector) : $(this).children(),
        $parents = $elems.parent();

    $parents.each(function(){
        $(this).children(selector).sort(function(){
            return Math.round(Math.random()) - 0.5;
        // }). remove().appendTo(this); // 2014-05-24: Removed `random` but leaving for reference. See notes under 'ANOTHER EDIT'
        }).detach().appendTo(this);
    });

    return this;
};

/**
$( document ).ready(function() {
	var list = $("#source .list-group-item").toArray();
	var elemlength = list.length;
	var timeout = null;
	var randomitem = null;
	var i = 1;

	function randomSelect(target){    
		$("#source .list-group-item").removeClass("list-group-item-danger"); 
		elemlength = list.length;
		randomnum = Math.floor(Math.random()*elemlength);
		randomitem = list[randomnum];
		randTimeout = Math.floor(Math.random() * (400 - 100) + 100);
		$(randomitem).toggleClass("list-group-item-danger");
		timeout = setTimeout(randomSelect, randTimeout);
		if(list.length == 0)
		{
			clearTimeout(timeout);
			list = jQuery.grep(list, function(value) {
				  return value != randomitem;
			});
			$(target).append($('#source .list-group-item-danger').removeClass('list-group-item-danger'));
		}
		else if(i++ % 20 == 0 && list.length != 0)
		{
			$(target).append($(randomitem).removeClass('list-group-item-danger'));
			list = jQuery.grep(list, function(value) {
				  return value != randomitem;
			});
		}
	}
	
// 	$('#btn-select').click(function(){
// 		i = 1;
// 		$('#table-draw').find('a').each(function(){
// 			randomSelect($(this));
// 		});
// 	});
	
	$('#btn-stop').click(function(){
		clearTimeout(timeout);
		list = jQuery.grep(list, function(value) {
			  return value != randomitem;
		});
// 		$('#result').append($('#source .list-group-item-danger').removeClass('list-group-item-danger'));
// 		randomSelect();
	});

});
$(function() {

	  $('#btn-select').click(function() {
		  alert("jklajkal");
	    $("#table-draw").randomize("td");
	  });

	});

	(function($) {

	$.fn.randomize = function(childElem) {
	  return this.each(function() {
	      var $this = $(this);
	      var elems = $this.children(childElem);

	      elems.sort(function() { return (Math.round(Math.random())-0.5); });  

	      $this.remove(childElem);  

	      for(var i=0; i < elems.length; i++)
	        $this.append(elems[i]);      

	  });    
	}
	})(jQuery);
**/
