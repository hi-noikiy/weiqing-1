<?php
global $_GPC, $_W;
$weid = $_W['uniacid'];
checklogin();
if (checksubmit('delete') && !empty($_GPC['select'])) {
foreach($_GPC['select'] as $row){
	$the_record2 = pdo_fetch("SELECT * FROM ".tablename('meepo_hongnianglikes')." WHERE weid=:weid AND id=:id",array(':weid'=>$weid,':id'=>intval($row)));
	pdo_query("UPDATE ".tablename('hnfans')." SET love = love - '{$the_record2['flower_num']}' WHERE from_user = '{$the_record2['toopenid']}' AND weid='{$weid}' ");
	pdo_delete('meepo_hongnianglikes',array('weid'=>$weid,'id'=>intval($row)));
	
}
message('删除成功！', $this->createWebUrl('flower_record'),'success');
}
load()->func('tpl');
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
//$status = isset($_GPC['status']) ? intval($_GPC['status']) : 0;
if($op=='display'){
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$condition = '';
if (!empty($_GPC['keyword'])) {
	$condition .= " AND a.nickname LIKE '%{$_GPC['keyword']}%'";
}	
$sql = "select * from ".tablename('meepo_hongnianglikes')."  where weid=:weid ORDER BY createtime DESC"
. " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
$list = pdo_fetchall($sql,array(':weid'=>$weid));
if(!empty($list)){
	foreach($list as $row){
		$row['from_user2'] = pdo_fetch("SELECT * FROM ".tablename('hnfans')." WHERE from_user=:from_user",array(':from_user'=>$row['openid']));
		$row['to_user'] = pdo_fetch("SELECT * FROM ".tablename('hnfans')." WHERE from_user=:from_user",array(':from_user'=>$row['toopenid']));
		$lists[] = $row;
	}
}
$total = pdo_fetchcolumn("select count(*)  from ".tablename('meepo_hongnianglikes')." where weid=:weid ORDER BY createtime DESC",array(':weid'=>$weid));
$pager = pagination($total, $pindex, $psize);
}elseif($op=='del'){
	$id = intval($_GPC['id']);
	$the_record = pdo_fetch("SELECT * FROM ".tablename('meepo_hongnianglikes')." WHERE weid=:weid AND id=:id",array(':weid'=>$weid,':id'=>$id));
	if(!empty($the_record)){
		pdo_delete('meepo_hongnianglikes',array('weid'=>$weid,'id'=>$id));
		pdo_query("UPDATE ".tablename('hnfans')." SET love = love - '{$the_record['flower_num']}' WHERE from_user = '{$the_record['toopenid']}' AND weid='{$weid}' ");
		message('删除成功',$this->createWebUrl('flower_record'));
	}else{
		message('错误、此项不存在或是已经被删除！',$this->createWebUrl('flower_record'),'error');
	}
	
}
include $this->template('flower_record');