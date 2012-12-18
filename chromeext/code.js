function scrapeskills (html) {
  var ret = {};
  var skills = html.match(/<tr><th colspan="2">([a-z-A-Z ]+)<\/th><\/tr>\s+<tr>\s+<td style="padding: 0;"><img style="width: 68px;" src="\/img\/powerups\/[^\.]+\.jpg" ?\/?><\/td>\s+<td style="padding: 0; padding-left: 5px;">\s+<div style="width: 90px;">\s+<div style="font-size: 9px; line-height: 10px; font-weight: normal; height: 20px; overflow: hidden;">[^<]+<\/div>\s+<div class="balones"><img src="\/img\/new\/sport_soccer.png" title="([0-9]+)%/g);
  if (!skills) {
    return ret;
  }
  for (var i=0; i < skills.length; i++) {
    var skill = skills[i].match(/<tr><th colspan="2">([a-z-A-Z ]+)<\/th><\/tr>\s+<tr>\s+<td style="padding: 0;"><img style="width: 68px;" src="\/img\/powerups\/[a-z_A-Z]+\.jpg" ?\/?><\/td>\s+<td style="padding: 0; padding-left: 5px;">\s+<div style="width: 90px;">\s+<div style="font-size: 9px; line-height: 10px; font-weight: normal; height: 20px; overflow: hidden;">[^<]+<\/div>\s+<div class="balones"><img src="\/img\/new\/sport_soccer.png" title="([0-9]+)%/);
    calc = Number(skill[2]);
    calc = calc/20;
    ret[skill[1]] = calc;
  }
  return ret;
}

function scrapeprogression (html) {
  var progr = html.match(/Progress.<\/div>\s+<div style="font-size: 40px; height: 64px; padding: 8px; border-top: 1px solid #000; line-height: 64px; background: #bbb; border-radius: 0px 0px 8px 8px; text-shadow: black 0px 1px 3px; color: #fff;"><div style="background:#888; border-radius: 8px;">([0-9]+)/);
  if (progr && progr[1]) {
    return Number(progr[1]);
  }
  return false;
}

function scrapeforecast(xml) {
  if (!xml) {
    return 0;
  }
  var forecast = 0;
  var tags = xml.getElementsByTagName('number');
  for (var i=0;i<tags.length;i++) {
    var val = Number(tags[i].firstChild.nodeValue);
    if (val > 100) {
      continue;
    }
    if (forecast < val) {
      forecast = val;
    }
  }
  return forecast;
}

function scrapepage (parenthtml) {
  var ret = {};
  var html = document.body.innerHTML;
  var team = html.match(/<a href="equipo.php\?id=([0-9]+)/);
  var parentteam = parenthtml.match(/<a class="color_skin" target="marco" href="equipo.php\?id=([0-9]+)/);
  if (team[1] != parentteam[1]) {
    return false; // we can only sell players on our own team
  }
  var id = location.search.match(/id_jugador=([0-9]+)/);
  ret.id = id[1];
  var pos = html.match(/<td>Position<\/td>\s+<td>([^<]+)<\/td>/);
  var stats = html.match(/<td>([a-zA-Z ]+)<\/td>\s+<td>\s+<span style="display: none;">(\d\d\d)<\/span>\s+<div class="jugbarra" style="width: 99px">\s+<div class="jugbarracar" style="border: 1px outset #[a-f0-9]+; width: \d+px; background: #[a-f0-9]+;"><\/div>\s+<div class="jugbarranum">\d+%/g);
  var summaries = html.match(/<td>([A-Za-z]+ (?:points|average))<\/td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">\.(\d+)<\/span>/g);
  var i;
  switch (pos[1]) {
    case "Goalkeeper" :
      ret["position"] = "GK";
      break;
    case "Left Back" :
      ret["position"] = "LB";
      break;
    case "Left Def." :
      ret["position"] = "LDF";
      break;
    case "Cent. Def." :
      ret["position"] = "CDF";
      break;
    case "Right Def." :
      ret["position"] = "RDF";
      break;
    case "Right Back" :
      ret["position"] = "RB";
      break;
    case "Left Mid." :
      ret["position"] = "LM";
      break;
    case "Left Inn. Mid." :
      ret["position"] = "LIM";
      break;
    case "Inn. Mid." :
      ret["position"] = "IM";
      break;
    case "Right Inn. Mid." :
      ret["position"] = "RIM";
      break;
    case "Right Mid." :
      ret["position"] = "RM";
      break;
    case "Left Wing." :
      ret["position"] = "LW";
      break;
    case "Left Forw." :
      ret["position"] = "LF";
      break;
    case "Cent. Forw." :
      ret["position"] = "CF";
      break;
    case "Right Forw." :
      ret["position"] = "RF";
      break;
    case "Right Wing." :
      ret["position"] = "RW";
      break;
    case "Offve. Mid." :
      ret["position"] = "OM";
      break;
    case "Def. Mid." :
      ret["position"] = "DFM";
      break;
  }
  ret.stats = {};
  for (i=0;i<stats.length;i++) {
    var inf = stats[i].match(/<td>([a-zA-Z ]+)<\/td>\s+<td>\s+<span style="display: none;">(\d\d\d)<\/span>\s+<div class="jugbarra" style="width: 99px">\s+<div class="jugbarracar" style="border: 1px outset #[a-f0-9]+; width: \d+px; background: #[a-f0-9]+;"><\/div>\s+<div class="jugbarranum">\d+%/);
    var res = inf[2]; // stat value
    if (res[0] == '0') {
      res = res[1] + res[2];
      if (res[0] == '0') {
        res = res[1];
      }
    }
    if (inf[1] == "Morale" || inf[1] == "Stamina" || inf[1] == "Versatility" || inf[1] == "Fitness") continue;
    ret.stats[inf[1]]  = Number(res);
  }
  var exp = html.match(/<td>Experience<\/td>\s+<td>([0-9\.,]+)/);
  ret["experience"] = Number(exp[1]);
  var age = html.match(/<td>([0-9]+) years/);
  ret["age"] = Number(age[1]);
  var inf = html.match(/<td>Total average<\/td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">\.(\d+)<\/span>/);
  if (inf[2] == "100") {
    ret["average"] = Number(inf[1] + 1);
  } else {
    ret["average"] = Number(inf[1] + "." + inf[2]);
  }
  return ret;
}

var player;
function sellPlayer()
{
  remote("confirm", player, function(result) {
    if (result.error) {
      alert(result.error.message);
    }
  });
}
var menu = document.getElementsByClassName("jugadormenuflotante")[0];
var el = document.createElement("a");
el.className = "boton";
el.href="#";
el.addEventListener("click", sellPlayer);
el.id = "gregslist";
el.appendChild(document.createTextNode("Add to Greg's List"));
menu.insertBefore(el, menu.firstChild);

chrome.extension.sendMessage([7], function(response) {
  player = response;
});