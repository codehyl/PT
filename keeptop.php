<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_UPLOADER)
    permissiondenied();
stdhead('保种TOP');
begin_main_frame('',true);
?>
<div style="width: 100%">
<h1 align="center">保种 TOP 100</h1>
<div style="margin-top: 8px">
<?php

$ord=$_REQUEST['ord']?$_REQUEST['ord']:'csize';
if (!in_array($ord,array("sl","tsize","osize","osl","csize","csl"))) $ord='tsize';
$limit=$_REQUEST['limit']?intval($_REQUEST['limit']):100;

$key="keepdataD_".$ord."_".$limit."_".date("Ymd");

print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=100%><tr>");
print("<td class=\"colhead\">排名</td>");
print("<td class=\"colhead\">".$lang_uploaders['col_username']."</td>");
print("<td class=\"colhead\"><a href='?ord=tsize&limit=$limit'><font color='#fff'>保种体积</font></a></td>");
print("<td class=\"colhead\"><a href='?ord=sl&limit=$limit'><font color='#fff'>保种数量</font></a></td>");
print("<td class=\"colhead\"><a href='?ord=osize&limit=$limit'><font color='#fff'>官种体积</font></a></td>");
print("<td class=\"colhead\"><a href='?ord=osl&limit=$limit'><font color='#fff'>官种数量</font></a></td>");
print("<td class=\"colhead\"><a href='?ord=csize&limit=$limit'><font color='#fff'>核算官种体积</font></a></td>");
print("<td class=\"colhead\"><a href='?ord=csl&limit=$limit'><font color='#fff'>核算官种数量</font></a></td>");
print("</tr>");
if (!$top100 = $Cache->get_value($key)) {
	$res = sql_query(" SELECT t1.userid,sum(t2.size) as tsize,count(t1.id) as sl,
    sum(case when t2.team in (6) then t2.size else 0 end)  as osize,
    sum(case when t2.team in (6) then 1 else 0 end)  as osl,
    sum(case when t2.team in (6) then t2.size else t2.size/4 end)  as csize,
    sum(case when t2.team in (6) then 1 else 0.25 end)  as csl
    FROM `peers` as t1 left join torrents as t2 on t1.torrent=t2.id where t1.seeder='yes' group by t1.userid having csize order by csize desc limit ".($_REQUEST['limit']?intval($_REQUEST['limit']):100));
    while($rs=mysql_fetch_array($res,MYSQL_ASSOC)){
        $mytop[]=$rs;
    }
    $top100=json_encode($mytop);
    $Cache->cache_value($key,$top100, 3600);
    }
    $top100=json_decode($top100,true);
    $i=0;
    foreach ($top100 as $row)
    {
	    $i++;
		print("<tr>");
        print("<td class=\"colfollow\">".$i."</td>");
		print("<td class=\"colfollow\">".get_username($row['userid'], false, true, true, false, false, true)."</td>");
        print("<td>".round($row['tsize']/1024/1024/1024/1024,2)." TB</td>");
        print("<td>".$row['sl']." </td>");
        print("<td>".round($row['osize']/1024/1024/1024/1024,2)." TB</td>");
        print("<td>".$row['osl']." </td>");
        print("<td>".round($row['csize']/1024/1024/1024/1024,2)." TB</td>");
        print("<td>".$row['csl']." </td>");
		print("</tr>");
	}
	print("</table>");
?>
</div>
</div>
<?php
end_main_frame();
stdfoot();
