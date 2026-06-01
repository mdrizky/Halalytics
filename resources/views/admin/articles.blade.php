@extends('admin.master')

@section('title', 'Article Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Articles</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Article Management</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Kelola konten edukasi, tips kesehatan, dan berita terbaru seputar ekosistem halal.</p>
    </div>
    <div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus"></i> Write New Article
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card" style="background: var(--primary-color); color: white;">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Articles</div>
            <div style="font-size: 28px; font-weight: 800;">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Published</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['published']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Drafts</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['draft']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Engagement (Views)</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['total_views']) }}</div>
        </div>
    </div>
</div>

<!-- Main Section: Tabs -->
<div class="tabs-container">
    <div class="tabs-header" style="display: flex; gap: 32px; border-bottom: 2px solid var(--border-color); margin-bottom: 24px; padding: 0 8px;">
        <button class="tab-btn active" data-tab="internal" style="padding: 12px 4px; background: none; border: none; font-weight: 700; font-size: 15px; color: var(--primary-color); border-bottom: 2px solid var(--primary-color); cursor: pointer; transition: all 0.3s; margin-bottom: -2px;">
            Local Articles <span class="badge badge-success" style="margin-left: 8px;">{{ $articles->total() }}</span>
        </button>
        <button class="tab-btn" data-tab="external" style="padding: 12px 4px; background: none; border: none; font-weight: 700; font-size: 15px; color: var(--text-muted); border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.3s; margin-bottom: -2px;">
            External Feed (Google News)
        </button>
    </div>

    <!-- Internal Articles Tab -->
    <div id="internal" class="tab-content active">
        <!-- Search & Filter Bar -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <form action="{{ route('admin.articles.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 16px; align-items: center;">
                    <div class="input-group" style="position: relative; margin-bottom: 0;">
                        <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or author..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                    </div>
                    
                    <select name="category" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                        <i class="fas fa-filter"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table style="border-collapse: collapse; width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding: 16px 24px;">Article Info</th>
                                <th style="padding: 16px;">Category</th>
                                <th style="padding: 16px;">Author</th>
                                <th style="padding: 16px; text-align: center;">Engagement</th>
                                <th style="padding: 16px;">Status</th>
                                <th style="padding: 16px 24px; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articles as $article)
                            <tr>
                                <td style="padding: 16px 24px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div style="width: 60px; height: 60px; border-radius: 12px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                            @if($article->image)
                                                <img src="{{ $article->image }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=A&background=2D6A4F&color=fff';">
                                            @else
                                                <i class="fas fa-newspaper" style="font-size: 20px; color: var(--text-muted);"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-main); font-size: 14px; line-height: 1.2;">{{ Str::limit($article->title, 50) }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Created {{ $article->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px;">
                                    <span class="badge" style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color); border: 1px solid var(--primary-color);">{{ strtoupper($article->category) }}</span>
                                </td>
                                <td style="padding: 16px; font-size: 13px; color: var(--text-main);">{{ $article->author }}</td>
                                <td style="padding: 16px; text-align: center;">
                                    <div style="font-size: 14px; font-weight: 800; color: var(--text-main);">{{ number_format($article->views) }}</div>
                                    <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Views</div>
                                </td>
                                <td style="padding: 16px;">
                                    @if($article->is_published)
                                        <span style="display: flex; align-items: center; gap: 6px; color: var(--success); font-size: 11px; font-weight: 800;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--success);"></span> PUBLISHED
                                        </span>
                                    @else
                                        <span style="display: flex; align-items: center; gap: 6px; color: var(--accent-color); font-size: 11px; font-weight: 800;">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--accent-color);"></span> DRAFT
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <form action="{{ route('admin.articles.toggle', $article->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--text-muted); border-color: var(--border-color);" title="{{ $article->is_published ? 'Switch to Draft' : 'Publish Article' }}">
                                                <i class="fas fa-{{ $article->is_published ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                        </form>
                                        <button onclick="editArticle({{ $article->id }}, this)" 
                                            data-title="{{ $article->title }}"
                                            data-category="{{ $article->category }}"
                                            data-author="{{ $article->author }}"
                                            data-content="{{ $article->content }}"
                                            data-image="{{ $article->image }}"
                                            class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--primary-color);">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Hapus artikel ini?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No articles found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="padding: 16px 24px;">
                {{ $articles->links() }}
            </div>
        </div>
    </div>

    <!-- External Feed Tab -->
    <div id="external" class="tab-content" style="display: none;">
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <form action="{{ route('admin.articles.index') }}" method="GET" style="display: flex; gap: 16px; align-items: center;">
                    <div class="input-group" style="position: relative; margin-bottom: 0; flex: 1;">
                        <i class="fas fa-globe" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" name="external_q" value="{{ $externalQuery }}" placeholder="Search global news by topic (e.g. Halal Food, Medicine)..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                        <i class="fas fa-search"></i> Search News
                    </button>
                </form>
            </div>
        </div>

        <div class="news-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
            @forelse($externalArticles ?? [] as $article)
            <div class="card news-card" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: all 0.3s; height: 100%; display: flex; flex-direction: column;">
                <div style="height: 180px; overflow: hidden; background: var(--bg-light); position: relative;">
                    @if(!empty($article['image_url']))
                        <img src="{{ $article['image_url'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">
                    @endif
                    <div style="position: absolute; top: 12px; left: 12px;">
                        <span class="badge" style="background: var(--primary-color); color: white;">{{ $article['source'] ?? 'News' }}</span>
                    </div>
                </div>
                <div class="card-body" style="flex: 1; display: flex; flex-direction: column; padding: 20px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--accent-color); margin-bottom: 8px;">{{ $article['published_label'] ?? 'Recently' }}</div>
                    <h3 style="font-size: 16px; color: var(--text-main); margin: 0 0 12px; line-height: 1.4;">{{ Str::limit($article['title'], 70) }}</h3>
                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 20px; flex: 1;">{{ Str::limit($article['excerpt'] ?? 'Click below to read the full story from the original source.', 120) }}</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); pt: 16px;">
                        <span style="font-size: 11px; color: var(--text-muted);">External Feed</span>
                        <a href="{{ $article['source_url'] }}" target="_blank" style="font-size: 13px; font-weight: 700; color: var(--primary-color); text-decoration: none;">Read More <i class="fas fa-external-link-alt" style="margin-left: 4px; font-size: 10px;"></i></a>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 64px; color: var(--text-muted);">No news items found.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 32px; border-radius: 16px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h2 style="margin: 0 0 8px; font-size: 22px; color: var(--primary-color);">Create New Article</h2>
        <p style="margin: 0 0 24px; color: var(--text-muted); font-size: 14px;">Tulis konten edukatif untuk komunitas Halalytics.</p>
        
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Title</label>
                    <input type="text" name="title" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Category</label>
                    <select name="category" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                        <option value="health">Kesehatan</option>
                        <option value="halal">Halal & Syariat</option>
                        <option value="nutrition">Nutrisi</option>
                        <option value="lifestyle">Gaya Hidup</option>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Cover Image URL (Optional)</label>
                    <input type="url" name="image" placeholder="https://..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Or Upload Image</label>
                    <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Content</label>
                <textarea name="content" rows="10" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none; resize: vertical; font-family: inherit;"></textarea>
            </div>
            
            <input type="hidden" name="author" value="{{ Auth::user()->full_name ?? Auth::user()->name }}">
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" name="status" value="draft" class="btn btn-outline" style="color: var(--accent-color); border-color: var(--accent-color);">Save Draft</button>
                <button type="submit" name="status" value="published" class="btn btn-primary">Publish Now</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 32px; border-radius: 16px; width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h2 style="margin: 0 0 8px; font-size: 22px; color: var(--primary-color);">Edit Article</h2>
        <p style="margin: 0 0 24px; color: var(--text-muted); font-size: 14px;">Perbarui konten artikel.</p>
        
        <form action="#" id="editArticleForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Title</label>
                    <input type="text" name="title" id="edit_title" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Category</label>
                    <select name="category" id="edit_category" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                        <option value="health">Kesehatan</option>
                        <option value="halal">Halal & Syariat</option>
                        <option value="nutrition">Nutrisi</option>
                        <option value="lifestyle">Gaya Hidup</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Author</label>
                <input type="text" name="author" id="edit_author" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Cover Image URL</label>
                    <input type="url" name="image" id="edit_image_url" placeholder="https://..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Change Image</label>
                    <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Content</label>
                <textarea name="content" id="edit_content" rows="10" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none; resize: vertical; font-family: inherit;"></textarea>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-tab');

                tabBtns.forEach(b => {
                    b.classList.remove('active');
                    b.style.color = 'var(--text-muted)';
                    b.style.borderBottomColor = 'transparent';
                });
                
                btn.classList.add('active');
                btn.style.color = 'var(--primary-color)';
                btn.style.borderBottomColor = 'var(--primary-color)';

                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                document.getElementById(target).style.display = 'block';
            });
        });
        
        // Handle URL parameter for active tab
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('external_q')) {
            document.querySelector('[data-tab="external"]').click();
        }
    });

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    function openEditModal() {
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function editArticle(id, btn) {
        const title = btn.getAttribute('data-title') || '';
        const category = btn.getAttribute('data-category') || 'health';
        const author = btn.getAttribute('data-author') || '';
        const content = btn.getAttribute('data-content') || '';
        const image = btn.getAttribute('data-image') || '';

        const form = document.getElementById('editArticleForm');
        form.action = `/admin/articles/${id}`;
        
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_author').value = author;
        document.getElementById('edit_content').value = content;
        document.getElementById('edit_image_url').value = image;
        
        openEditModal();
    }
</script>
@endpush
