<?php

namespace cardbase
{
	function init() {}

	function get_user_cards($username){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		$result = $db->query("SELECT * FROM {$gtablepre}users WHERE username='$username'");
		$pu = $db->fetch_array($result);
		extract($pu,EXTR_PREFIX_ALL,'p');
		$carr = explode('_',$p_cardlist);
		return $carr;
	}	

	function get_card($ci,$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if ($pa==NULL){
			$n=$name;
		}else{
			if (isset($pa['username'])) $n=$pa['username'];
			else $n=$pa['name'];
		}
		$cn=$carddesc[$ci]['name'];
		$result = $db->query("SELECT * FROM {$gtablepre}users WHERE username='$n'");
		$pu = $db->fetch_array($result);
		extract($pu,EXTR_PREFIX_ALL,'p');
		$carr = explode('_',$p_cardlist);
		$clist = Array();
		foreach($carr as $key => $val){
			$clist[$key] = $val;
		}
		if (in_array($ci,$clist)){
			return 0;
		}else{
			$p_cardlist.="_".$ci;
			$db->query("UPDATE {$gtablepre}users SET cardlist='$p_cardlist' WHERE username='$n'");
			return 1;
		}
	}
	
	function get_qiegao($num,$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		if ($pa==NULL){
			$n=$name;
		}else{
			if (isset($pa['username'])) $n=$pa['username'];
			else $n=$pa['name'];
		}
		$result = $db->query("SELECT gold FROM {$gtablepre}users WHERE username='$n'");
		$cg = $db->result($result,0);
		$cg=$cg+$num;
		if ($cg<0) $cg=0;
		$db->query("UPDATE {$gtablepre}users SET gold='$cg' WHERE username='$n'");
	}
	
	function calc_qiegao_drop(&$pa,&$pd,&$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('cardbase','sys','logger','map'));
		$qiegaogain=0;
		if (!in_array($gametype,$qiegao_ignore_mode)){		
			if (($pd['type']==90)&&(($areanum/$areaadd)<1)&&(rand(0,99)<10)){//杂兵
				$qiegaogain=rand(7,15);
			}
			if (($pd['type']==2)&&(($areanum/$areaadd)<1)){//幻象
				$qiegaogain=rand(9,19);
			}
		}
		return $qiegaogain;
	}
	
	function player_kill_enemy(&$pa,&$pd,$active){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($pa, $pd, $active);
		eval(import_module('cardbase','sys','logger','map'));
		$qiegaogain=calc_qiegao_drop($pa,$pd,$active);
		if ($qiegaogain>0){
			get_qiegao($qiegaogain,$pa);
			$log.="<span class=\"orange\">敌人掉落了{$qiegaogain}单位的切糕！</span><br>";
		}
	}	
	
	function itemmix_success()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		/*eval(import_module('sys','player','logger','map','cardbase'));
		if (!in_array($gametype,$qiegao_ignore_mode)){
			if (($itm0=="绝冲大剑【神威】")&&(($areanum/$areaadd)<2)){
				if (get_card(42)==1){
					$log.="恭喜您获得了活动奖励卡<span class=\"orange\">Fleur</span>！<br>";
				}else{
					$log.="您已经拥有活动奖励卡了，系统奖励您<span class=\"yellow\">100</span>切糕！<br>";
					get_qiegao(100);
				}
			}
		}*/
		$chprocess();	
	}
	
	function get_card_pack($card_pack_name) {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('cardbase'));
		$card_pack = Array();
		foreach ($carddesc as $ci => $card) {
			if ($card["pack"] == $card_pack_name)
				$card_pack[$ci] = $card;
		}
		//return  json_encode($card_pack, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."test";
		return $card_pack;
	}

	function get_card_pack_list() {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('cardbase'));
		return $packlist;
	}


	function in_card_pack($packname) {
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('cardbase'));
		return in_array($packname, $packlist);
	}
	
	function kuji($type, &$pa, $is_dryrun = false){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('cardbase'));
		$ktype=(int)$type;
		$func='kuji'.$ktype.'\\kujidraw'.$ktype;
		if (defined('MOD_KUJI'.$ktype)) {
			$kr=$func($pa, $is_dryrun);
			if (!is_array($kr)){
				if ($kr==-1){
					return -1;
				}else{
					$dr=array($kr);
				}
			}else{
				$dr=$kr;
			}
			return $dr;
		}
		return -1;
	}
}

?>