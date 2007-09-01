/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Get current time as UNIX Timestamp
 * @return  int
 */
function unixTimeStamp(){
  var now=new Date();
  return Math.round(now.getTime()/1000);
}


/**
 * Format a time/date.
 * Returns a string formatted according to the given format
 * string using the given integer timestamp or the current local
 * time if no timestamp is given.
 * More info at: http://www.php.net/manual/en/function.date.php
 * @param   string    format      Date format
 * @param   int       timestamp   Optional UNIX timestamp
 * @return  string    Formatted date/time
 */
function date(format, timestamp){
  var result='';
  if(typeof(format)=='string' && format!=''){
    if(typeof(timestamp)=='string'){
      timestamp=stringToNumber(timestamp);
    }
    if(typeof(timestamp)!='number' || timestamp<0){
      timestamp=unixTimeStamp();
    }
    var tmp, tmp1, tmp2, tmp3, tmp4 ='';
    var timeHandle=new Date(timestamp*1000); // Requested date
    var timeHandleYearStart=new Date('Jan 1 '+timeHandle.getFullYear()+' 00:00:00'); // First day of the requested year
    var weekDaysFull=new Array('Sunday', 'Monday', 'Tuesday', 'Wednsday', 'Thursday', 'Friday', 'Saturday');
    var weekDaysShort=new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    var monthsFull=new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    var monthsShort=new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    
    var dParam=new Array();
    // Day of the month, 2 digits with leading zeros.
    // 01 to 31
    dParam['d']=str_pad(timeHandle.getDate(), 2, '0', STR_PAD_LEFT);
    // A textual representation of a day, three letters.
    // Mon through Sun
    dParam['D']=weekDaysShort[timeHandle.getDay()];
    // Day of the month without leading zeros.
    // 1 to 31
    dParam['j']=timeHandle.getDate().toString();
    // A full textual representation of the day of the week.
    // Sunday through Saturday
    dParam['l']=weekDaysFull[timeHandle.getDay()];
    // ISO-8601 numeric representation of the day of the week
    // 1 (for Monday) through 7 (for Sunday)
    dParam['N']=timeHandle.getDay();
    if(dParam['N']==0){
      dParam['N']=7;
    }
    dParam['N']+='';
    // English ordinal suffix for the day of the month, 2 characters
    // st, nd, rd or th. Works well with j
    dParam['S']='';
    tmp=timeHandle.getDate();
    if(tmp>10 && tmp<21){
      dParam['S']='th';
    }else{
      if(tmp>9){
        tmp+='';
        tmp=tmp.substring(tmp.length-1);
      }
      switch(tmp){
        case '1' :  dParam['S']='st';
                    break;
        case '2' :  dParam['S']='nd';
                    break;
        case '3' :  dParam['S']='rd';
                    break;
        default  :  dParam['S']='th';
                    break;
      }
    }
    // Numeric representation of the day of the week
    // 0 (for Sunday) through 6 (for Saturday)
    dParam['w']=timeHandle.getDay().toString();
    // The day of the year (starting from 0)
    // 0 through 365
    dParam['z']=''+Math.floor((timeHandle-timeHandleYearStart)/86400000);
    // ISO-8601 week number of year, weeks starting on Monday
    // Example: 42 (the 42nd week in the year)
    dParam['W']='';
    tmp2=1;
    do{
      tmp=new Date('Jan '+tmp2+' '+timeHandle.getFullYear()+' 00:00:00');
      if(tmp.getDay()==1){
        dParam['W']=1+Math.floor((((timeHandle-tmp)/1000)/86400)/7);
        dParam['W']+='';
        break;
      }
      tmp2++;
    }while(true);
    // A full textual representation of a month, such as January or March
    // January through December
    dParam['F']=monthsFull[timeHandle.getMonth()];
    // Numeric representation of a month, with leading zeros
    // 01 through 12
    dParam['m']=str_pad(timeHandle.getMonth()+1, 2, '0', STR_PAD_LEFT);
    // A short textual representation of a month, three letters
    // Jan through Dec
    dParam['M']=monthsShort[timeHandle.getMonth()];
    // Numeric representation of a month, without leading zeros
    // 1 through 12
    dParam['n']=(timeHandle.getMonth()+1).toString();
    // Number of days in the given month
    // 28 through 31
    dParam['t']=32-new Date(timeHandle.getFullYear(), timeHandle.getMonth(), 32).getDate();
    dParam['t']+='';
    // Whether it's a leap year
    // 1 if it is a leap year, 0 otherwise.
    dParam['L']=((timeHandle.getFullYear()%4 == 0) && ((timeHandle.getFullYear()%100 != 0) || (timeHandle.getFullYear()%400 == 0)))? '1' : '0';
    // ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.
    // Examples: 1999 or 2003
    dParam['o']=timeHandle.getFullYear().toString();
    // A full numeric representation of a year, 4 digits
    // Examples: 1999 or 2003
    dParam['Y']=timeHandle.getFullYear().toString();
    // A two digit representation of a year
    // Examples: 99 or 03
    dParam['y']=timeHandle.getFullYear().toString().substring(2);
    // Lowercase Ante meridiem and Post meridiem
    // am or pm
    dParam['a']='';
    tmp=timeHandle.getHours();
    if(tmp<12){
      dParam['a']='am';
    }else{
      dParam['a']='pm';
    }
    // Uppercase Ante meridiem and Post meridiem
    // AM or PM
    dParam['A']='';
    tmp=timeHandle.getHours();
    if(tmp<12){
      dParam['A']='AM';
    }else{
      dParam['A']='PM';
    }
    // Swatch Internet time
    // 000 through 999
    dParam['B']='';
    // 12-hour format of an hour without leading zeros
    // 1 through 12
    dParam['g']=timeHandle.getHours();
    if(dParam['g']>12){
      dParam['g']-=12;
    }
    // 24-hour format of an hour without leading zeros
    // 0 through 23
    dParam['G']=timeHandle.getHours();
    // 12-hour format of an hour with leading zeros
    // 01 through 12
    dParam['h']=timeHandle.getHours();
    if(dParam['h']>12){
      dParam['h']-=12;
    }
    dParam['h']=str_pad(dParam['h'], 2, '0', STR_PAD_LEFT);
    // 24-hour format of an hour with leading zeros
    // 00 through 23
    dParam['H']=str_pad(timeHandle.getHours(), 2, '0', STR_PAD_LEFT)
    // Minutes with leading zeros
    // 00 to 59
    dParam['i']=str_pad(timeHandle.getMinutes(), 2, '0', STR_PAD_LEFT)
    // Seconds, with leading zeros
    // 00 through 59
    dParam['s']=str_pad(timeHandle.getSeconds(), 2, '0', STR_PAD_LEFT)
    // Timezone identifier
    // Examples: UTC, GMT, Atlantic/Azores
    dParam['e']='';
    tmp=timeHandle.toString().split(' ');
    for(var i=0; i<tmp.length; i++){
      if(tmp[i].indexOf('+')!=-1 || tmp[i].indexOf('-')!=-1){
        tmp=tmp[i].split('-');
        tmp=tmp[0].split('+');
        dParam['e']=tmp[0];
        break;
      }
    }
    // Whether or not the date is in daylights savings time
    // 1 if Daylight Savings Time, 0 otherwise.
    dParam['I']='';
    tmp1=new Date(timeHandle.getFullYear(), 0, 1, 0, 0, 0, 0);
    tmp2=new Date(timeHandle.getFullYear(), 6, 1, 0, 0, 0, 0);
    tmp=tmp1.toGMTString();
    tmp3=new Date(tmp.substring(0, tmp.lastIndexOf(' ')-1));
    tmp=tmp2.toGMTString();
    tmp4=new Date(tmp.substring(0, tmp.lastIndexOf(' ')-1));
    dParam['I']=((tmp1-tmp3)!=(tmp2-tmp4))? '1' : '0';
    // Difference to Greenwich time (GMT) in hours
    // Example: +0200
    dParam['O']='';
    tmp=timeHandle.getTimezoneOffset();
    tmp1=(tmp<0)? '+' : '-';
    tmp=Math.sqrt(tmp*tmp);
    tmp2=str_pad(Math.round(tmp/60), 2, '0', STR_PAD_LEFT);
    tmp3=str_pad(tmp-tmp2*60, 2, '0', STR_PAD_LEFT);
    dParam['O']=tmp1+tmp2+tmp3;
    // Difference to Greenwich time (GMT) with colon between hours and minutes
    // Example: +02:00
    dParam['P']='';
    tmp=timeHandle.getTimezoneOffset();
    tmp1=(tmp<0)? '+' : '-';
    tmp=Math.sqrt(tmp*tmp);
    tmp2=str_pad(Math.round(tmp/60), 2, '0', STR_PAD_LEFT);
    tmp3=str_pad(tmp-tmp2*60, 2, '0', STR_PAD_LEFT);
    dParam['P']=tmp1+tmp2+':'+tmp3;
    // Timezone setting
    // Examples: EST, MDT ...
    dParam['T']='???'; // ToDo
    // Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.
    // -43200 through 43200
    dParam['Z']=(-60*timeHandle.getTimezoneOffset()).toString();
    // ISO 8601 date
    // Example: 2004-02-12T15:19:21+00:00
    dParam['c']= timeHandle.getFullYear()
                +'-'+str_pad(timeHandle.getMonth(), 2, '0', STR_PAD_LEFT)
                +'-'+str_pad(timeHandle.getDay(), 2, '0', STR_PAD_LEFT)
                +'T'+str_pad(timeHandle.getHours(), 2, '0', STR_PAD_LEFT)
                +':'+str_pad(timeHandle.getMinutes(), 2, '0', STR_PAD_LEFT)
                +':'+str_pad(timeHandle.getSeconds(), 2, '0', STR_PAD_LEFT);
    // RFC 2822 formatted date
    // Example: Thu, 21 Dec 2000 16:01:07 +0200
    dParam['r']=timeHandle.toString();
    // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
    // UNIX timestamp.
    dParam['U']=unixTimeStamp();

    // Create new string
    for(i=0; i<format.length; i++){
      if(typeof(dParam[format.charAt(i)])!='undefined' && dParam[format.charAt(i)]!=''){
        result+=dParam[format.charAt(i)];
      }else{
        result+=format.charAt(i);
      }
    }
  }
  return result;
}

