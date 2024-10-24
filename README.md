# Imagebord-Webapp

# 概要

ユーザーが画像やテキストコンテンツを投稿できるイメージボード Webアプリです。ユーザーは、画像と共にコンテンツを投稿することで新しいスレッドを作成できます。ユーザーがメインスレッドを開始し、他のユーザーがそれに返信できるスレッドベースのディスカッションをすることができます。またメインスレッドが作成されると、他のユーザーはテキストや画像を使ってそれに返信できます。

# URL

[ここをクリックするとwebアプリケーションサイトに飛べます
](https://imageboard.mdtohtml.com)

# Demo

## スレッドの作成




https://github.com/user-attachments/assets/a1b56baa-7d6d-47bf-89af-38785aee1eb6




## スレッド一覧の表示




https://github.com/user-attachments/assets/48910711-520c-48a1-9671-b51888fe6ab7





## スレッドへの返信



https://github.com/user-attachments/assets/dc189f2a-d022-49d4-926d-bec7864f97b4







# 使い方
## スレッドの作成
ホームページから新しいスレッドを作成ボタンをクリックし各入力欄を入力してください
![スクリーンショット 2024-10-24 15 51 44](https://github.com/user-attachments/assets/4406a107-7b04-4bab-9a2a-67af2acbdf0e)



| 入力項目 | 内容 |
| ---- | ---- |
| 件名 | スレッドのタイトルを入力 |
| 投稿内容 | コメントを入力 |
| 画像 | 投稿したい画像をファイルを選択(png, jpeg, gif に対応) |

入力内容に問題がある場合は問題のあるフォームにエラー内容が表示されます。

## スレッド
スレッド一覧からスレッドのタイトルを押すと各スレッドの詳細のページが見れます。
ここでは以下の情報が表示されます。
- 件名
- 投稿内容
- 画像
- スレッドへの全ての返信

## スレッド一覧ページ

![スクリーンショット 2024-10-24 15 48 20](https://github.com/user-attachments/assets/99216deb-7f9f-48f1-83d9-c028396113e1)


このページでは今までに作られたスレッドの一覧を見ることができます。
それぞれのスレッドにはそのスレッドへの最新の返信が最大５個表示されています。




# ER図
![Blank board](https://github.com/user-attachments/assets/268f731f-9d86-4e6b-882a-27d6d663fc4d)


# 使用技術
- PHP
- MySQL
- HTML
- CSS
- TailwindCSS
- Javascript
- Amazon EC2
- Amazon RDB
- NGINX

