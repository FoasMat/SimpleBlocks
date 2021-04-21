<div class="container mt-3">

    <h3>{{title}}</h3>
    <form class="row g-3" method="post">
        <input type="hidden" name="id" value="{{item_id}}"/>
        <input type="hidden" name="data" value="true"/>

        <div class="col-12">
            <label for="name">Info:</label>
            <textarea disabled class="form-control" value=" ">{{item_code}}</textarea>    
            
        </div>

        <div class="col-12">
            <label for="name">Name block</label>
            <input type="text" class="form-control" name="name" value="{{item_secondname}}">
        </div>

        
        <div class="col-12">
            <label for="body">Content</label>
            <?php
                $args = array('textarea_name' => 'body', 'editor_class' => 'form-control');
                $content = '{{item_body}}';
                wp_editor($content, 'content', $args); 
             ?>
            
        </div>

        <div class="col-12">
            <label for="description">Description</label>
            <textarea id="description" name="desc" class="form-control">{{item_desc}}</textarea>
        </div>

        <div class="col-6">
            <label for="isActive">Is active?</label>
            <input type="checkbox" name="isActive" {{item_checked}} value="1" class="form-control">
        </div>

        <button class="btn btn-primary mt-2" type="submit">Save</button>
    </form>
</div>