/****************************************************************************
 * Copyleft lostpassword                                                    *
 * [gdb.lost@gmail.com]                                                     *
 *                                                                          *
 *                                                                          *
 * misTET is free software: you can redistribute it and/or modify           *
 * it under the terms of the GNU General Public License as published by     *
 * the Free Software Foundation, either version 3 of the License, or        *
 * (at your option) any later version.                                      *
 *                                                                          *
 * misTET is distributed in the hope that it will be useful,                *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 * GNU General Public License for more details.                             *
 *                                                                          *
 * You should have received a copy of the GNU General Public License        *
 * along with misTET.  If not, see <http://www.gnu.org/licenses/>.          *
 ****************************************************************************/

misTET.res.create("logger", {
        config: { },
});

misTET.modules.create("logger", {

    version: "0.1.0",

    needs: ["security"],

    initialize: function () {
                
        try {
            misTET.res.loadXML("logger", this.root + "/resources/config.xml");
        } catch (e) {
            misTET.errors.create(e);
        }
                
        Event.observe(document, ":change", function (event) {
            misTET.modules['logger'].execute(["page", "view", event.memo]);
        });
                
        Event.observe(document, ":error", function (error) {
            misTET.modules['logger'].execute(["error", error.memo]);
        });
                        

    },

    execute: function (args) {
    
        var argv = '';
                
        for (var i = 0; i < $A(arguments).length; i++) {
            argv += i + "=" + encodeURIComponent((typeof($A(arguments)[i]) != "object") ? $A(arguments)[i] : Object.toJSON($A(arguments)[i])) + "&";
        }
        argv = argv.slice(0, argv.length -1);
    
        var date = encodeURIComponent(new Date().toString());
    
        new Ajax.Request(this.root+"/logger.php?data&" + argv + "&date=" + date, {
            method: "get"
        });
    }
});
