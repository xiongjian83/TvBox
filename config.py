ip_version_priority = "ipv6"

source_urls = [
    "https://raw.githubusercontent.com/MemoryCollection/IPTV/refs/heads/main/itvlist.txt",
    "https://raw.githubusercontent.com/kimwang1978/collect-tv-txt/main/merged_output.txt"
    "https://raw.githubusercontent.com/xiongjian83/iptv/main/speedtest/zubo.txt",
    "https://live.zbds.top/tv/iptv6.txt",  
    "https://live.zbds.top/tv/iptv4.txt", 
    "https://raw.githubusercontent.com/xiongjian83/iptv/main/xj.txt",  
]

url_blacklist = [
    "epg.pw/stream/",
    "103.40.13.71:12390",
    "[2409:8087:1a01:df::4077]/PLTV/",
    "8.210.140.75:68",
    "154.12.50.54",
    "yinhe.live_hls.zte.com",
    "8.137.59.151",
    "[2409:8087:7000:20:1000::22]:6060",
    "histar.zapi.us.kg",
    "www.tfiplaytv.vip",
    "dp.sxtv.top",
    "111.230.30.193",
    "148.135.93.213:81",
    "live.goodiptv.club",
    "iptv.luas.edu.cn"
]

announcements = [
    {
        "channel": "公告",
        "entries": [
            {"name": "更新日期", "url": "http://159.75.85.63:35455/huya/11282233", "logo": "http://175.178.251.183:6689/LR.jpg"},
            {"name": None, "url": "http://159.75.85.63:35455/huya/11342387", "logo": "http://175.178.251.183:6689/LR.jpg"}
  ]
    }
]

epg_urls = [
    "https://live.fanmingming.com/e.xml",
    "http://epg.51zmt.top:8000/e.xml",
    "http://epg.aptvapp.com/xml",
    "https://epg.pw/xmltv/epg_CN.xml",
    "https://epg.pw/xmltv/epg_HK.xml",
    "https://epg.pw/xmltv/epg_TW.xml"
]