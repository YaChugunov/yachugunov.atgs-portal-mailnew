<?php
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
#
#

#
# 
# ### ### ### ### ### ### ### ### ### ### ### ### ### ### ### 
?>
<style>
#usermessages-topmain {
    font-family: 'Stolzl Book', sans-serif;
    font-size: 12px;
    height: 110px;
    background-color: #F1F1F1;
}

#usermessages-topmain img.logo {
    width: 64px;
    height: 64px;
}

#usermessages-topmain .usermessages-title {
    font-family: 'Stolzl Book', sans-serif;
}

#usermessages-topmain .usermessages-title h1 {
    font-size: 2.0rem;
    color: #FFFFFF;
}

#usermessages-topmain .usermessages-title h3 {
    font-size: 0.8rem;
    color: #FFFFFF;
    opacity: 0.65;
}

div#listMessages-icon:hover {
    cursor: pointer;
}

div#userSettings-icon:hover {
    cursor: pointer;
}

#usermessages-sideblock.sideblock {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 12;
    top: 110px;
    right: 0;
    background-color: #F1F1F1;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 40px;
}

#listMessages-sideblock-output {
    opacity: 0;
    transition: 1.0s;
}

#usermessages-sideblock.sideblock .title {
    font-family: 'HeliosCond', sans-serif;
    font-size: 1.25rem;
    font-weight: 600;
    color: #343a40;
    padding: 8px;
    margin-left: 16px;
    /* background-color: #FFFFFF; */
    white-space: nowrap;
    /* border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem; */
}

#usermessages-sideblock.sideblock a {
    color: #AAAAAA;
    transition: 0.3s;
}

#usermessages-sideblock.sideblock a:hover,
#usermessages-sideblock.sideblock a.nav-link.subnav:hover {
    color: #FFFFFF;
}

#usermessages-sideblock.sideblock .closebtn {
    position: relative;
    top: -15px;
    left: 5px;
    font-size: 2rem;
}

#usermessages-sideblock.sideblock a.nav-link {
    font-family: 'Stolzl Book', sans-serif;
    font-size: 0.85rem;
    white-space: nowrap;
}

#usermessages-sideblock.sideblock a.nav-link.subnav {
    font-size: 0.8rem;
    color: #888888;
    padding: 2px 8px 2px 32px;
}

@media screen and (max-height: 450px) {
    #usermessages-sideblock.sideblock {
        padding-top: 15px;
    }

    #usermessages-sideblock.sideblock a {
        font-size: 16px;
    }
}
</style>


<div id="usermessages-sideblock" class="sideblock">
    <a href="javascript:void(0)" class="closebtn" onclick="closeUserMessagesSideblock()">&times;</a>
    <div class="title">Уведомления пользователя</div>
    <div id="listMessages-sideblock-output"></div>
</div>

<script type="text/javascript" language="javascript" class="init">
function openUserMessagesSideblock() {
    document.getElementById("usermessages-sideblock").style.width = "450px";
    document.getElementById("listMessages-sideblock-output").style.opacity = "1";
    ajaxRequest_getListMessagesSideblock('getListMessages');
}

function closeUserMessagesSideblock() {
    document.getElementById("usermessages-sideblock").style.width = "0";
    document.getElementById("listMessages-sideblock-output").style.opacity = "0";
}
</script>