

For local use
0. 这一版，游戏开始时 LCC engine 默认建立 game_institution，所有 welcome.php 依照该默认直接开始创建首个 agent，这样虽然可以运行，并正确获得mysql，但是，经过实验，要想具备获得正确的 “ 下一步 ” 回馈的能力，就必须在游戏开始时重新建立一个 institution。
1. The php files will use mysql and csv to store messages and player information.
2. Do not need to use file to store interid. But if necessary, they can use the trsinfo.txt and resinfo.txt to store
3. The php files are be used by multiple players during the same time.
