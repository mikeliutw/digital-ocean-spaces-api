# digital-ocean-spaces-api

由於最近網站空間越來越不夠，開始使用Digital Ocean 的Space 空間來存放檔案，他的API 存取方法與AWS 的S3 是一樣的，由於維護的網站太舊無法用SDK 來操作，所以使用API的方式，一開始研究還真是超級複雜的想說不就用一個TOKEN就好了，用自己獨有的驗證方式，網路上爬了２天才找到解決方式，預防我自己失意，也希望可以幫助的其他人，附上PHP的程式碼，以下有上傳的API與取得目錄檔案列表的API，上傳檔案自動是public read