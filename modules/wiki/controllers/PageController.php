<?php
/**
 * @author Skorobogatko Alexei <skorobogatko.oleksii@gmail.com>
 * @copyright 2016
 * @since 0.2
 */

namespace modules\wiki\controllers;

use app\base\Controller;
use modules\wiki\forms\DeleteWiki;
use modules\wiki\forms\Editor;
use modules\wiki\models\History;
use modules\wiki\models\Wiki;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * PageController
 *
 * @author skoro
 */
class PageController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'markdown-preview' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'raw', 'markdown-preview', 'create', 'wiki-suggest'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'view', 'raw', 'markdown-preview',
                            'wiki-suggest',
                        ],
                        'roles' => ['viewWiki'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createWiki'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Wiki index.
     * Shows all root pages.
     */
    public function actionIndex()
    {
        $rootPages = Wiki::findAllRoot();
        
        return $this->render('index', [
            'rootPages' => $rootPages,
        ]);
    }
    
    /**
     * View wiki page.
     * @param integer $id wiki page id
     */
    public function actionView($id)
    {
        /** @var $wiki Wiki */
        $wiki = $this->findModel(Wiki::className(), $id);
        
        return $this->render('view', [
            'wiki' => $wiki,
        ]);
    }
    
    /**
     * Create root or child page.
     * @param integer $id wiki parent page id
     */
    public function actionCreate($id = null)
    {
        $wiki = new Wiki();
        if ($id) {
            /** @var $parent Wiki */
            $parent = $this->findModel(Wiki::className(), $id);
            $wiki->parent_id = $parent->id;
        }
        
        $editor = new Editor($wiki);
        $editor->summary = Yii::t('app', 'Page created.');
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($editor->load($post) && $editor->save()) {
                return $this->redirect(['page/view', 'id' => $editor->getWiki()->id]);
            }
        }
        
        return $this->render('create', [
            'editor' => $editor,
        ]);
    }
    
    /**
     * Update wiki page.
     * @param integer $id wiki page id
     * @param integer $rev history revision id
     */
    public function actionUpdate($id, $rev = null)
    {
        /** @var $wiki Wiki */
        $wiki = $this->findModel(Wiki::className(), $id);
        if (!Yii::$app->user->can('updateWiki', ['wiki' => $wiki])) {
            throw new ForbiddenHttpException();
        }
        $editor = new Editor($wiki);
        
        if ($rev) {
            /** @var $history History */
            $history = History::findOne([
                'wiki_id' => $id,
                'rev' => $rev,
            ]);
            if (!$history) {
                $this->addFlash(self::FLASH_WARNING, Yii::t('app', 'Revision not found.'));
            }
            $editor->content = $history->content;
        }
        
        $historyProvider = new ActiveDataProvider([
            'query' => History::find()->where([
                'wiki_id' => $id,
            ])->orderBy('created_at DESC'),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($editor->load($post) && $editor->save()) {
                return $this->redirect(['page/view', 'id' => $editor->getWiki()->id]);
            }
        }
        
        return $this->render('update', [
            'editor' => $editor,
            'historyProvider' => $historyProvider,
        ]);
    }
    
    /**
     * View raw markup.
     * @param integer $id wiki page id
     */
    public function actionRaw($id)
    {
        /** @var $wiki Wiki */
        $wiki = $this->findModel(Wiki::className(), $id);
        $this->layout = false;
        if ($history = $wiki->historyLatest) {
            print '<pre>' . $history->content . '</pre>';
        }
    }
    
    /**
     * Delete wiki page.
     * @param integer $id wiki page id
     */
    public function actionDelete($id)
    {
        /** @var $wiki Wiki */
        $wiki = $this->findModel(Wiki::className(), $id);
        
        if (!Yii::$app->user->can('deleteWiki', ['wiki' => $wiki])) {
            throw new ForbiddenHttpException();
        }

        $delete = new DeleteWiki($wiki);
        if (Yii::$app->request->isPost) {
            if (!$delete->isChildrenExists() && $wiki->delete()) {
                $this->addFlash(self::FLASH_SUCCESS, Yii::t('app', 'Page <em>{title}</em> deleted.', [
                    'title' => $wiki->title,
                ]));
                return $this->redirect('index');
            }
            $post = Yii::$app->request->post();
            if ($delete->load($post) && $delete->delete()) {
                $this->addFlash(self::FLASH_SUCCESS, Yii::t('app', 'Page <em>{title}</em> deleted.', [
                    'title' => $delete->getWiki()->title,
                ]));
                if ($delete->mode == DeleteWiki::DELETE_CHILDREN) {
                    return $this->redirect('index');
                }
                return $this->redirect(['view', 'id' => $delete->parentId]);
            }
            $this->addFlash(self::FLASH_ERROR, Yii::t('app', 'Cannot delete page.'));
        }
        
        return $this->render('delete', [
            'delete' => $delete,
        ]);
    }
    
    /**
     * Preview generated html from markdown text.
     * @return string
     */
    public function actionMarkdownPreview()
    {
        $this->layout = false;
        $content = Yii::$app->request->post('content', '');
        return Yii::$app->formatter->asMarkdown($content);
    }
    
    /**
     * Autocomplete wiki title.
     * @param integer $ign ignore suggests from wiki page id
     * @param string $q
     */
    public function actionWikiSuggest($ign = '', $q = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Wiki::find()
                ->andFilterWhere(['like', 'title', $q])
                ->orderBy('title')
                ->limit(10);
        
        if ($ign) {
            /** @var $wiki Wiki */
            $wiki = $this->findModel(Wiki::className(), $ign);

            $children = $wiki->getChildrenAll(function ($query) {
                $query->select('id');
            });
            
            $childrenIds = array_map(function ($child) {
                return $child->id;
            }, $children);
            
            $query->where(['!=', 'id', $wiki->id])
                  ->andWhere(['not in', 'id', $childrenIds]);  
        }
        
        $wikis = $query->all();
        
        return array_map(function (Wiki $wiki) {
            return [
                'id' => $wiki->id,
                'text' => $wiki->title,
            ];
        }, $wikis);
    }
}
