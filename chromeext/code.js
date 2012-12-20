var player = {
  el: null,
  player: {},
  isours: false,
  exists: false,
  getSaleMessage: function()
  {
    if (this.exists) {
      return "Update Listing [ML]";
    } else {
      return "List For Sale [ML]";
    }
  },
  setupElement: function(self)
  {
    
  },
  checkExists: function()
  {
    var self = this;
    remote("exists", {'id': this.player.id}, function(result) {
      if (result.error) {
        alert(result.error.message);
        return;
      }
      if (result.params.exists) {
        self.exists = true; // ensure we get an "update this" menu item
      }
    });
  },
  createElement: function()
  {
    var self = this;
    return function() {
      if (self.el) {
        return;
      }
      if (!self.isours) return;
  
      var menu = document.getElementsByClassName("jugadormenuflotante")[0];
      self.el = document.createElement("a");
      self.el.className = "boton";
      self.el.href="#";
      self.el.addEventListener("click", self.sellPlayer());
      self.el.id = "gregslist";
      self.el.appendChild(document.createTextNode(self.getSaleMessage()));
      menu.insertBefore(self.el, menu.firstChild);
    };
  },
  updateLink: function()
  {
    this.el.firstChild.innerHTML = this.getSaleMessage();
  },
  codes: {},
  sellPlayer: function()
  {
    var self = this;
    return function() {
      if (!self.isours) return;
      var musthavecode = false;
      if (self.exists) {
        musthavecode = true;
      }
      chrome.storage.sync.get(['SMGregsList.codes'], function(a) {
        self.codes = a['SMGregsList.codes'];
      });
      if (!self.codes) {
        self.codes = {};
      }
      if (self.codes[self.player.id]) {
        self.player.code = self.codes[self.player.id];
        musthavecode = false;
      }
      if (musthavecode) {
        self.player.code = prompt("Please enter the player update code", self.player.code);
        if (!self.player.code) {
          alert("Cannot update the listing without a player code");
          return;
        }
      }
      if (!self.player.forecast) {
        self.player.forecast = prompt("Do you know the forecast of your player?", 0);
      }
      if (!self.player.progression) {
        self.player.progression = prompt("Do you know the progression of your player?", 0);
      }
      remote("confirm", self.player, function(result) {
        if (result.error) {
          alert(result.error.message);
        } else {
          alert("Successfully listed player for sale.  Update code needed to make changes is: " + result.params.code);
          self.codes[result.params.id] = result.params.code;
          self.player.code = result.params.code;
          chrome.storage.sync.set({'SMGregsList.codes': self.codes});
          self.updateLink();
        }
      });
    }
  },
  scrapeskills: function (html) {
    var ret = {};
    var skills = html.match(/<tr><th colspan="2">([a-z-A-Z ]+)<\/th><\/tr>\s+<tr>\s+<td style="padding: 0;"><img style="width: 68px;" src="\/img\/powerups\/[^\.]+\.jpg" ?\/?><\/td>\s+<td style="padding: 0; padding-left: 5px;">\s+<div style="width: 90px;">\s+<div style="font-size: 9px; line-height: 10px; font-weight: normal; height: 20px; overflow: hidden;">[^<]+<\/div>\s+<div class="balones"><img src="\/img\/new\/sport_soccer.png" title="([0-9]+)%/g);
    if (!skills) {
      this.player.skills = ret;
      return ret;
    }
    for (var i=0; i < skills.length; i++) {
      var skill = skills[i].match(/<tr><th colspan="2">([a-z-A-Z ]+)<\/th><\/tr>\s+<tr>\s+<td style="padding: 0;"><img style="width: 68px;" src="\/img\/powerups\/[a-z_A-Z]+\.jpg" ?\/?><\/td>\s+<td style="padding: 0; padding-left: 5px;">\s+<div style="width: 90px;">\s+<div style="font-size: 9px; line-height: 10px; font-weight: normal; height: 20px; overflow: hidden;">[^<]+<\/div>\s+<div class="balones"><img src="\/img\/new\/sport_soccer.png" title="([0-9]+)%/);
      calc = Number(skill[2]);
      calc = calc/20;
      ret[skill[1]] = calc;
    }
    this.player.skills = ret;
    return ret;
  },
  scrapeprogression: function (html) {
    var progr = html.match(/Progress.<\/div>\s+<div style="font-size: 40px; height: 64px; padding: 8px; border-top: 1px solid #000; line-height: 64px; background: #bbb; border-radius: 0px 0px 8px 8px; text-shadow: black 0px 1px 3px; color: #fff;"><div style="background:#888; border-radius: 8px;">([0-9]+)/);
    if (progr && progr[1]) {
      this.player.progression = Number(progr[1]);
      return Number(progr[1]);
    }
    this.player.progression = 0;
    return false;
  },
  scrapeforecast: function (xml) {
    if (!xml) {
      this.player.forecast = 0;
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
    this.player.forecast = forecast;
    return forecast;
  },
  scrapepage: function (parenthtml) {
    var ret = {};
    var html = document.body.innerHTML;
    var team = html.match(/<a href="equipo.php\?id=([0-9]+)/);
    var parentteam = parenthtml.match(/<a class="color_skin" target="marco" href="equipo.php\?id=([0-9]+)/);
    var id = location.search.match(/id_jugador=([0-9]+)/);
    ret.id = id[1];
    this.player.id = ret.id;
    if (team[1] != parentteam[1]) {
      this.isours = false;
      this.checkExists(); // we will use this to display the for sale icon on other team's players
      return false; // we can only sell players on our own team
    }
    this.isours = true;
    this.checkExists();
    var pos = html.match(/<td>Position<\/td>\s+<td>([^<]+)<\/td>/);
    var stats = html.match(/<td>([a-zA-Z ]+)<\/td>\s+<td>\s+<span style="display: none;">(\d\d\d)<\/span>\s+<div class="jugbarra" style="width: 99px">\s+<div class="jugbarracar" style="border: 1px outset #[a-f0-9]+; width: \d+px; background: #[a-f0-9]+;"><\/div>\s+<div class="jugbarranum">\d+%/g);
    var summaries = html.match(/<td>([A-Za-z]+ (?:points|average))<\/td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">\.(\d+)<\/span>/g);
    var i;
    switch (pos[1]) {
      case "Goalkeeper" :
        ret.position = "GK";
        break;
      case "Left Back" :
        ret.position = "LB";
        break;
      case "Left Def." :
        ret.position = "LDF";
        break;
      case "Cent. Def." :
        ret.position = "CDF";
        break;
      case "Right Def." :
        ret.position = "RDF";
        break;
      case "Right Back" :
        ret.position = "RB";
        break;
      case "Left Mid." :
        ret.position = "LM";
        break;
      case "Left Inn. Mid." :
        ret.position = "LIM";
        break;
      case "Inn. Mid." :
        ret.position = "IM";
        break;
      case "Right Inn. Mid." :
        ret.position = "RIM";
        break;
      case "Right Mid." :
        ret.position = "RM";
        break;
      case "Left Wing." :
        ret.position = "LW";
        break;
      case "Left Forw." :
        ret.position = "LF";
        break;
      case "Cent. Forw." :
        ret.position = "CF";
        break;
      case "Right Forw." :
        ret.position = "RF";
        break;
      case "Right Wing." :
        ret.position = "RW";
        break;
      case "Offve. Mid." :
        ret.position = "OM";
        break;
      case "Def. Mid." :
        ret.position = "DFM";
        break;
    }
    this.player.position = ret.position;
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
    this.player.stats = ret.stats;
    var exp = html.match(/<td>Experience<\/td>\s+<td>([0-9\.,]+)/);
    ret.experience = Number(exp[1]);
    this.player.experience = ret.experience;
    var age = html.match(/<td>([0-9]+) years/);
    ret.age = Number(age[1]);
    this.player.age = ret.age;
    var inf = html.match(/<td>Total average<\/td>\s+<td class="numerico">\s+(\d+)<span style="font-size: 0.7em;">\.(\d+)<\/span>/);
    if (inf[2] == "100") {
      ret.average = Number(inf[1] + 1);
    } else {
      ret.average = Number(inf[1] + "." + inf[2]);
    }
    this.player.average = ret.average;
    return ret;
  }
}

var menu = document.getElementsByClassName("jugadormenuflotante")[0].parentNode.firstChild.nextSibling;
menu.addEventListener("mouseover", player.createElement());