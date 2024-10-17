<?php

namespace Itokotaro\ImageWebapp\Models;

class Post
{
    private ?int $id;  // idをnullableに変更
    private ?int $replyToId;
    private ?string $subject;
    private string $content;
    private ?string $imagePath;
    private string $createdAt;

    public function __construct(?int $id, ?int $replyToId, ?string $subject, string $content, ?string $imagePath, string $createdAt)
    {
        $this->id = $id;
        $this->replyToId = $replyToId;
        $this->subject = $subject;
        $this->content = $content;
        $this->imagePath = $imagePath;
        $this->createdAt = $createdAt;
    }

    // ゲッターとセッターを追加
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReplyToId(): ?int
    {
        return $this->replyToId;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath ?: null;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setReplyToId(?int $replyToId): void
    {
        $this->replyToId = $replyToId;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath ?? '';
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
