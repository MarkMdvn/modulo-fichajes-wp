		function flipTo(digit, n){
			var current = digit.attr('data-num');
			digit.attr('data-num', n);
			digit.find('.front').attr('data-content', current);
			digit.find('.back, .under').attr('data-content', n);
			digit.find('.flap').css('display', 'block');
			setTimeout(function(){
				digit.find('.base').text(n);
				digit.find('.flap').css('display', 'none');
			}, 350);
		}

		function jumpTo(digit, n){
			digit.attr('data-num', n);
			digit.find('.base').text(n);
		}

		function updateGroup(group, n, flip){
			var digit1 = jQuery('.ten'+group);
			var digit2 = jQuery('.'+group);
			n = String(n);
			if(n.length == 1) n = '0'+n;
			var num1 = n.substr(0, 1);
			var num2 = n.substr(1, 1);
			if(digit1.attr('data-num') != num1){
				if(flip) flipTo(digit1, num1);
				else jumpTo(digit1, num1);
			}
			if(digit2.attr('data-num') != num2){
				if(flip) flipTo(digit2, num2);
				else jumpTo(digit2, num2);
			}
		}

		function setTime(flip){
			var t = new Date();
			
			var hours = t.getHours();
			var minutes = t.getMinutes();
			var ampm = hours >= 12 ? 'P' : 'A';
			var ampmm = hours >= 12 ? 'M' : 'M';
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
			minutes = minutes < 10 ? '0'+minutes : minutes;

			updateGroup('hour', hours, flip);
			updateGroup('min', minutes, flip);
			updateGroup('sec', t.getSeconds(), flip);
			updateGroup('ampm', ampm, flip);
			updateGroup('ampmm', ampmm, flip);
		}

		jQuery(document).ready(function(){			
			setTime(false);
			setInterval(function(){
				setTime(true);
			}, 1000);	
		});