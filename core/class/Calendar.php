<?php
	class Calendar
	{
		function set_date($date)
		{
			$this->date = strtotime($date);
			$this->first = strtotime( date('Y',$this->date).'-'. date('m',$this->date).'-01');
		}
		
		function set_event($date, $string)
		{
			$this->events[$date][] = $string;	
		}
		
		
		function generate()
		{
			if ($this->date) {
				$year = date('Y', $this->first);
				$month = date('m', $this->first);
				
				$num_days_in_month = date('t', $this->first);
				$day_of_week = date('w', $this->first);
				
				$calendar_days = ceil(($day_of_week+$num_days_in_month) / 7) * 7;
				$html .= "<table class='calendar' cellspacing='0' cellpadding='0'>";				
				$html .= "<tr>
							<td class='day-label'>Sunday</td>
							<td class='day-label'>Monday</td>
							<td class='day-label'>Tuesday</td>
							<td class='day-label'>Wednesday</td>
							<td class='day-label'>Thursday</td>
							<td class='day-label'>Friday</td>
							<td class='day-label'>Saturday</td>
						</tr>";
				
				for ($i=0; $i< 42; $i++)
				{
					$event = '';
					
					$day_num = $i%7;
					$day = $i - $day_of_week + 1;
					
					if ($day_num == 0) {
						$html .= "<tr>";	
					}
					if ($day >= 1 && $day <= $num_days_in_month) {
						$date = $year.'-'.$month.'-'.str_pad($day, 2, 0, STR_PAD_LEFT);
						
						$today = $date == date('Y-m-d') ? 'today' : '';
						
						if ($this->events[$date]) {
							$event = implode('<br/>', $this->events[$date]);
						}
						
						$html .= "<td class='in-month $today'><div class='day'>$day</div>$event</td>";
					} else {
						$html .= "<td class='out-month'>&nbsp;</td>";	
					}
					
					
					
					if ($day_num ==6) {
						$html .= "</tr>";	
					}
				}
				
				$html .= "</table>";
				
				return $html;
			}
		}
	}
?>