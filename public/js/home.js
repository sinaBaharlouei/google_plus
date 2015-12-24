
var posts_object = new Array();
var hot_objects = new Array();
var likers = new Array();
var comments = new Array();
var posts;
var xmlhttp = new XMLHttpRequest();
var index = 0;
var post_index = 0;
var hot_index = 0;

if(localStorage.getItem("hots") && localStorage.getItem("normal") && localStorage.getItem("likers") && localStorage.getItem("comments") && false) {

    posts_object = JSON.parse(localStorage.getItem("normal"));
    hot_objects = JSON.parse(localStorage.getItem("hots"));
    likers = JSON.parse(localStorage.getItem("likers"));
    comments = JSON.parse(localStorage.getItem("comments"));

    // Create the page
    createPage();
    newPost();
}

else {
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var xmlDoc = xmlhttp.responseXML;
	        alert(xmlDoc);
            posts = xmlDoc.getElementsByTagName("posts")[0];
            var items = posts.getElementsByTagName("post");
            for(var i= 0; items.length > i; i++) {
                if(i!=5 && i!=9 && i!=11)
                    getPostXML(items[i].innerHTML);
            }

            localStorage.setItem('hots', JSON.stringify(hot_objects));
            localStorage.setItem('normal', JSON.stringify(posts_object));
            localStorage.setItem('likers', JSON.stringify(likers));
            localStorage.setItem('comments', JSON.stringify(comments));

            createPage();
        }
    };
    xmlhttp.open("POST", "http://plus.local/post/getposts", true);
    xmlhttp.send();
}


function getPostXML(xml_file) {

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            xmlDoc = xmlhttp.responseXML;
            post = xmlDoc.getElementsByTagName("post")[0];
            var isHot = post.getAttribute("hot");

            var tag_xml = post.getElementsByTagName('tag');
            var tag;
            if(tag_xml.length > 0) {
                tag = tag_xml[0].innerHTML;
            }
            else tag = "";
            var comment_xml = post.getElementsByTagName('comments');
            var comments_number=0, comments_link="";
            if(comment_xml.length > 0 ) {
                comments_number = comment_xml[0].getAttribute("commentNumber");
                comments_link = comment_xml[0].getAttribute("allCommentsLink");
            }
            else {
                comments_number = 0;
                comments_link = '-';
            }

            var share_tag = post.getElementsByTagName('share');
            var share = "";
            if(share_tag.length > 0)
                share = share_tag[0].innerHTML;

            var object = {
                author: post.getElementsByTagName('author')[0].innerHTML,
                tag: tag,
                proofilepic: post.getElementsByTagName('proofilepic')[0].innerHTML,
                date: post.getElementsByTagName('date')[0].innerHTML,
                image: post.getElementsByTagName('image')[0].innerHTML,
                share: share,
                text: post.getElementsByTagName('text')[0].innerHTML,
                like_number: post.getElementsByTagName('like')[0].getAttribute("likeNumber"),
                comments_number: comments_number,
                comments_link: comments_link,
                index: index
            };

            var like_element = post.getElementsByTagName('like')[0];
            var likers_element = like_element.getElementsByTagName("likers")[0];
            var likers_object = new Array();
            for(var j=0; likers_element.childNodes.length > j; j++) {

                var liker_element = likers_element.childNodes[j];
                if (liker_element.nodeType === 1) {
                    var liker = {
                        name: liker_element.innerHTML,
                        image: liker_element.getAttribute("image")
                    };
                    likers_object.push(liker);
                }

            }
            likers.push(likers_object);
            var comments_element = post.getElementsByTagName('comments')[0];
            var comments_object = new Array();
            for(j=0; j<comments_element.childNodes.length;j++) {

                var comment_element = comments_element.childNodes[j];
                if(comment_element.nodeType == 1) {
                    var tex = comment_element.getElementsByTagName("text")[0].innerHTML;

                    var comment = {
                        text: tex,
                        image: comment_element.getElementsByTagName("image")[0].innerHTML,
                        date: comment_element.getElementsByTagName("date")[0].innerHTML,
                        name: comment_element.getElementsByTagName("name")[0].innerHTML,
                        index: index
                    };
                    comments_object.push(comment);
                }
            }
            comments.push(comments_object);

            if(isHot != "yes") {
                posts_object.push(object);
            }
            else hot_objects.push(object);
            index++;
        }
    };
    xmlhttp.open("POST", xml_file, false);
    xmlhttp.send();
}


function createPage() {
    viewMore();
}

function createPost (x,  div_number) {

            // In first span
            var first_div = document.getElementById("div" + div_number);

            var new_box = document.createElement("div");
            new_box.className = "post_box";

            new_box.style.position = "relative";
            if(x==1) {
                new_box.id = "anim1";
            }
            else if(x==2) {
                new_box.id = "anim2";
            }
            var post_title = document.createElement('div');
            post_title.className = "post_title";

            var profile = document.createElement('img');
            profile.className = "profile";
            profile.alt = "-";
            profile.src = posts_object[x].proofilepic;

            var texts = document.createElement('div');
            texts.className = "texts";

            var br = document.createElement("br");
            var name = document.createElement("span");
            name.className = "name";
            name.innerHTML = posts_object[x].author;

            var shared = document.createElement("span");
            shared.className = "shared";
            shared.innerHTML = "Shared publicly - " + posts_object[x].date;

            texts.appendChild(br);
            texts.appendChild(name);
            texts.appendChild(shared);

            post_title.appendChild(profile);
            post_title.appendChild(texts);

            new_box.appendChild(post_title);

            var add = document.createElement('button');
            add.className = "add";

            var button_image = document.createElement('img');
            button_image.src = "/images/Home%20Pic/12.png";
            button_image.alt = "user";
            add.appendChild(button_image);

            new_box.appendChild(add);

            var post_text = document.createElement("div");
            post_text.className = "post_text";

            post_text.innerHTML = posts_object[x].text;

            var new_br = document.createElement("br");
            var new_br2 = document.createElement("br");
            post_text.appendChild(new_br);
            post_text.appendChild(new_br2);

            var tag = document.createElement("tag");
            tag.className = "tag";
            var tags = posts_object[x].tag;
            tags = tags.substr(1);
            var res_tags = tags.split("#");
            var i;
            if(res_tags[0] != "")
                for(i=0;i<res_tags.length;i++) {
                    setTag(res_tags[i], post_text);
               }

            new_box.appendChild(post_text);

            var new_br3 = document.createElement("br");
            var new_br4 = document.createElement("br");
            post_text.appendChild(new_br3);
            post_text.appendChild(new_br4);

            var post_image = document.createElement("img");
            post_image.src = posts_object[x].image;
            post_image.alt = "-";
            post_image.className = "post_img";

            new_box.appendChild(post_image);
            // create two br
            var br1 = document.createElement("br");
            var br2 = document.createElement("br");
            new_box.appendChild(br1);
            new_box.appendChild(br2);

            // Likes button
            var like_button = document.createElement("button");
            like_button.className = "likes";
            like_button.onclick = function() {
                likePost(posts_object[x].index, false);
            };
            var like_image = document.createElement("img");
            like_image.src = "/images/Home%20Pic/8.png";
            like_image.alt = "-";
            like_image.className = "btn_img inline";

            var liker_number = document.createElement("span");
            liker_number.setAttribute("isLiked", "no");
            liker_number.id = "post_like" + posts_object[x].index;

            liker_number.className = "btn_text";
            liker_number.innerHTML = posts_object[x].like_number;

            like_button.appendChild(like_image);
            like_button.appendChild(liker_number);

            new_box.appendChild(like_button);
            // shares
            var share_button = document.createElement("button");
            share_button.className = "likes";
            share_button.onclick = function() {
                sharePost(posts_object[x].index, false);
            };

            var share_image = document.createElement("img");
            share_image.src = "/images/Home%20Pic/7.png";
            share_image.alt = "-";
            share_image.className = "btn_img inline";

            var share_number = document.createElement("span");
            share_number.setAttribute("isShared", "no");
            share_number.id = "post_share" + posts_object[x].index;
            share_number.className = "btn_text";
            share_number.innerHTML = posts_object[x].share;

            share_button.appendChild(share_image);
            share_button.appendChild(share_number);

            new_box.appendChild(share_button);

            var friend_images = document.createElement("div");
            friend_images.className = "friend_images";

            for(var j =0; j< likers[x].length; j++ ) {
                // Add liker image
                var liker_name = likers[x][j].name;
                var liker_image = likers[x][j].image;
                var likerImg = document.createElement("img");
                likerImg.src = liker_image;
                likerImg.alt = liker_name;
                likerImg.className = "friend_image";
                friend_images.appendChild(likerImg);
            }
            new_box.appendChild(friend_images);


            var comment_box = document.createElement("div");
            comment_box.className = "comment_box";
            comment_box.id = "box" + posts_object[x].index;

            var comment_numbers = document.createElement("span");
            comment_numbers.className = "number";
            comment_numbers.innerHTML = posts_object[x].comments_number + " comments";
            comment_box.appendChild(comment_numbers);

            var comment_index = posts_object[x].index;
            var arrow_image = document.createElement("img");
            arrow_image.className = "arrow";
            arrow_image.src = "/images/Home%20Pic/13.PNG";
            arrow_image.alt = "-";
            arrow_image.id = "comment" + posts_object[x].index;
            arrow_image.onclick = function() {
                showComments(comment_index, false, posts_object[x].comments_link);
            };

            comment_box.appendChild(arrow_image);

            var br11 = document.createElement("br");
            var br12 = document.createElement("br");
            comment_box.appendChild(br11);
            comment_box.appendChild(br12);

            var other_comments_div = document.createElement("div");
            other_comments_div.id = "other_comment" + posts_object[x].index;
            other_comments_div.style.display = "none";
            comment_box.appendChild(other_comments_div);

            var first_comments_div = document.createElement("div");
            first_comments_div.id = "first_comment" + posts_object[x].index;
            first_comments_div.style.display = "inherit";
            comment_box.appendChild(first_comments_div);

            // add comments here

            for(j = 0; j < comments[comment_index].length; j++) {

                // image of commenter
                var image_div = document.createElement("div");
                var comment_image = document.createElement("img");
                comment_image.src = comments[comment_index][j].image;
                comment_image.alt = "";
                comment_image.className = "comment";
                image_div.appendChild(comment_image);
                first_comments_div.appendChild(image_div);

                var comment_texts = document.createElement("div");
                comment_texts.className = "texts";

                var commenter = document.createElement("span");
                commenter.className = "commenter";
                commenter.innerHTML = comments[comment_index][j].name;

                var date = document.createElement("span");
                date.className = "time";
                date.innerHTML = "&nbsp &nbsp"  + comments[comment_index][j].date;
                commenter.appendChild(date);

                comment_texts.appendChild(commenter);

                var comment_div = document.createElement("div");
                comment_div.className = "comment_div";

                var comment_text = document.createElement("span");
                comment_text.className = "comment_text";
                comment_text.innerHTML = comments[comment_index][j].text;

                comment_div.appendChild(comment_text);
                comment_texts.appendChild(comment_div);
                first_comments_div.appendChild(comment_texts);

                var br13 = document.createElement("br");
                first_comments_div.appendChild(br13);

            }

            // add input box
            var input_box = document.createElement("input");
            input_box.className = "new_comment";
            input_box.placeholder = " Add a comment ...";
            comment_box.appendChild(input_box);
            new_box.appendChild(comment_box);
            first_div.appendChild(new_box);
}

function createHotPost(x, div_num) {


    var span12 = document.getElementById("div_hot" + div_num);

    var post_box = document.createElement("post_box");
    post_box.style.position = "relative";
    post_box.className = "post_box";

    var content_part = document.createElement("div");
    content_part.className = "content_part";

    post_box.appendChild(content_part);
    span12.appendChild(post_box);

    var index_hot = hot_objects[x].index;

    var br1 = document.createElement("br");
    content_part.appendChild(br1);

    var inner = document.createElement("div");
    inner.className = "inner";

    var image_div = document.createElement("div");
    image_div.className = "image_div";

    var post_image = document.createElement("img");
    post_image.className = "big_post_img";
    post_image.src = hot_objects[x].image;
    post_image.alt = "-";

    image_div.appendChild(post_image);
    inner.appendChild(image_div);

    var comment_link = document.createElement("a");
    comment_link.href = "#";
    comment_link.className = "comment_link";
    inner.appendChild(comment_link);

    var br2 = document.createElement("br");
    inner.appendChild(br2);
    var br3 = document.createElement("br");
    inner.appendChild(br3);
    content_part.appendChild(inner);

    var comment_part = document.createElement("div");
    comment_part.className = "comment_part";

    var post_title = document.createElement("div");
    post_title.className = "post_title margin_top";

    var profile_pic = document.createElement("img");
    profile_pic.className = "profile";
    profile_pic.src = hot_objects[x].proofilepic;
    profile_pic.alt = "-";

    post_title.appendChild(profile_pic);

    var texts = document.createElement("div");
    texts.className = "texts";

    var br4 = document.createElement("br");
    texts.appendChild(br4);

    var name = document.createElement("span");
    name.className = "name";
    name.innerHTML = hot_objects[x].author;
    texts.appendChild(name);

    var shared = document.createElement("span");
    shared.className = "shared";
    shared.innerHTML = "Shared privately - " + hot_objects[x].date;
    texts.appendChild(shared);

    post_title.appendChild(texts);
    comment_part.appendChild(post_title);

    var post_text = document.createElement("p");
    post_text.className = "post_text";
    var temp = hot_objects[x].text.substr(9);
    temp = temp.substring(0, temp.length -3);
    post_text.innerHTML = temp;

    var tags = hot_objects[x].tag;
    tags = tags.substr(1);
    var res_tags = tags.split("#");
    var i;
    if(res_tags.length > 1)
        for(i=0;i<res_tags.length;i++) {
            setTag(res_tags[i], post_text);
        }

    comment_part.appendChild(post_text);

    var likes = document.createElement("button");
    likes.className = "likes";
    likes.onclick = function() {
        likePost(hot_objects[x].index, true);
    };
    var like_image = document.createElement("img");
    like_image.className = "btn_img inline";
    like_image.src = "/images/Home%20Pic/8.png";
    like_image.alt = "-";
    // set like function
    likes.appendChild(like_image);

    var like_numbers = document.createElement("span");
    like_numbers.setAttribute("isLiked", "no");
    like_numbers.id = "hot_like" + hot_objects[x].index;
    like_numbers.className = "btn_text";
    like_numbers.innerHTML = hot_objects[x].like_number;
    likes.appendChild(like_numbers);

    var shares = document.createElement("button");
    shares.className = "likes";
    shares.onclick = function() {
        sharePost(hot_objects[x].index, true);
    };
    var share_image = document.createElement("img");
    share_image.className = "btn_img inline";
    share_image.src = "/images/Home%20Pic/7.png";
    share_image.alt = "-";
    // set like function
    shares.appendChild(share_image);

    var share_numbers = document.createElement("span");
    share_numbers.setAttribute("isShared", "no");
    share_numbers.id = "hot_share" + hot_objects[x].index;
    share_numbers.className = "btn_text";
    share_numbers.innerHTML = hot_objects[x].share;
    shares.appendChild(share_numbers);

    comment_part.appendChild(post_text);
    comment_part.appendChild(likes);
    comment_part.appendChild(shares);


    var comment_box = document.createElement("div");
    comment_box.className = "comment_box border";
    comment_box.id = "hot_comment" + index_hot;
    comment_part.appendChild(comment_box);

    var number = document.createElement("span");
    number.className = "number blue";
    number.innerHTML = hot_objects[x].comments_number + " comments";
    comment_box.appendChild(number);

    var img = document.createElement("img");
    img.src = "/images/Home%20Pic/13.PNG";
    img.alt = '-';
    img.className = "arrow";
    img.onclick = function() {
        showComments(index_hot, true, hot_objects[x].comments_link);
    };
    comment_box.appendChild(img);

    var br100 = document.createElement("br");
    var br101 = document.createElement("br");
    comment_box.appendChild(br100);
    comment_box.appendChild(br101);

    var other_comments_div = document.createElement("div");
    other_comments_div.id = "other_comment" + hot_objects[x].index;
    other_comments_div.style.display = "none";
    comment_box.appendChild(other_comments_div);

    var first_comments_div = document.createElement("div");
    first_comments_div.id = "first_comment" + hot_objects[x].index;
    first_comments_div.style.display = "inherit";
    comment_box.appendChild(first_comments_div);

    // add comments here

    for(var j = 0; j < comments[index_hot].length; j++) {

        // image of commenter
        var image_div1 = document.createElement("div");
        var comment_image = document.createElement("img");
        comment_image.src = comments[index_hot][j].image;
        comment_image.alt = "";
        comment_image.className = "comment";
        image_div1.appendChild(comment_image);
        first_comments_div.appendChild(image_div1);

        var comment_texts = document.createElement("div");
        comment_texts.className = "texts";

        var commenter = document.createElement("span");
        commenter.className = "commenter";
        commenter.innerHTML = comments[index_hot][j].name;

        var date = document.createElement("span");
        date.className = "time";
        date.innerHTML = "&nbsp &nbsp"  + comments[index_hot][j].date;
        commenter.appendChild(date);

        comment_texts.appendChild(commenter);

        var comment_div = document.createElement("div");
        comment_div.className = "comment_div";

        var comment_text = document.createElement("span");
        comment_text.className = "comment_text";
        comment_text.innerHTML = comments[index_hot][j].text;

        comment_div.appendChild(comment_text);
        comment_texts.appendChild(comment_div);
        first_comments_div.appendChild(comment_texts);

        var br13 = document.createElement("br");
        first_comments_div.appendChild(br13);

    }

    var input = document.createElement("input");
    input.className = "new_comment";
    input.placeholder = "Add a comment ...";

    post_box.appendChild(comment_part);
}

function likePost(index, isHot) {

    var element;
    if(isHot)
        element = document.getElementById("hot_like" + index);
    else
        element = document.getElementById("post_like" + index);

    var likes = parseInt(element.innerHTML);
    if(element.getAttribute("isLiked") == "yes") {
        likes--;
        element.setAttribute("isLiked", "no");
    }

    else {
        likes++;
        element.setAttribute("isLiked", "yes");
    }
    element.innerHTML = "" + likes;
}

function sharePost(index, isHot) {

    var element;
    if(isHot)
        element = document.getElementById("hot_share" + index);
    else
        element = document.getElementById("post_share" + index);

    var share = 0;
    if(element.innerHTML != "")
        share = parseInt(element.innerHTML);

    if(element.getAttribute("isShared") == "yes") {
        share--;
        element.setAttribute("isShared", "no");
    }

    else {
        share++;
        element.setAttribute("isShared", "yes");
    }
    element.innerHTML = "" + share;
}


function showComments(index, isHot, link) {


    // show more comments
    var comment_box = document.getElementById("other_comment"+ index) ;
    var first_comment_box = document.getElementById("first_comment"+ index) ;

    if(first_comment_box.style.display == "none") {
        // hide
        comment_box.innerHTML = "";
        comment_box.style.display = "none";
        first_comment_box.style.display = "inherit";
    }
    else {

        var httpReq = new XMLHttpRequest();
        httpReq.onreadystatechange = function () {
            if (httpReq.readyState == 4 && httpReq.status == 200) {
                var doc = httpReq.responseXML;
                var comments1 = doc.getElementsByTagName("comments")[0];

                var comms = comments1.getElementsByTagName("comment");

                // add comments here

                for(var i=0; i<comms.length; i++) {

                    if(comms[i].parentNode.nodeName == "comments") {

                        var commenter_name = comms[i].getElementsByTagName("name")[0].innerHTML;
                        var commenter_image = comms[i].getElementsByTagName("image")[0].innerHTML;
                        var commenter_date = comms[i].getElementsByTagName("date")[0].innerHTML;
                        var commenter_text = comms[i].getElementsByTagName("text")[0].innerHTML;
                        commenter_text = commenter_text.substr(9);
                        commenter_text = commenter_text.substring(0, commenter_text.length-3);

                        var reply = comms[i].getElementsByTagName("reply");

                        // image of commenter
                        var image_div = document.createElement("div");
                        var comment_image = document.createElement("img");
                        comment_image.src = commenter_image;
                        comment_image.alt = "";
                        comment_image.className = "comment";
                        image_div.appendChild(comment_image);
                        comment_box.appendChild(image_div);

                        var comment_texts = document.createElement("div");
                        comment_texts.className = "texts";

                        var commenter = document.createElement("span");
                        commenter.className = "commenter";
                        commenter.innerHTML = commenter_name;

                        var date = document.createElement("span");
                        date.className = "time";
                        date.innerHTML = "&nbsp &nbsp"  + commenter_date;
                        commenter.appendChild(date);

                        comment_texts.appendChild(commenter);

                        var comment_div = document.createElement("div");
                        comment_div.className = "comment_div";

                        var comment_text = document.createElement("span");
                        comment_text.className = "comment_text";
                        comment_text.innerHTML = commenter_text;

                        comment_div.appendChild(comment_text);
                        comment_texts.appendChild(comment_div);
                        comment_box.appendChild(comment_texts);

                        if(reply.length > 0) {
                            var reply_comment = reply[0].getElementsByTagName("comment")[0];
                            var image_div1 = document.createElement("div");
                            image_div1.style.marginLeft = "13%";
                            image_div1.style.marginTop = "3%";
                            var comment_image1 = document.createElement("img");
                            comment_image1.src = reply_comment.getElementsByTagName("image")[0].innerHTML;
                            comment_image1.alt = "";
                            comment_image1.className = "comment margin_left";
                            image_div1.appendChild(comment_image1);
                            comment_box.appendChild(image_div1);

                            var comment_texts1 = document.createElement("div");
                            comment_texts1.className = "texts";

                            var rep_text = reply_comment.getElementsByTagName("text")[0].innerHTML;
                            var rep = rep_text.substr(9);
                            rep = rep.substring(0, rep.length -3);

                            var commenter1 = document.createElement("span");
                            commenter1.className = "commenter";
                            commenter1.innerHTML = reply_comment.getElementsByTagName("name")[0].innerHTML;

                            var date1 = document.createElement("span");
                            date1.className = "time";
                            date1.innerHTML = "&nbsp &nbsp"  + reply_comment.getElementsByTagName("date")[0].innerHTML;
                            commenter1.appendChild(date1);

                            comment_texts1.appendChild(commenter1);

                            var comment_div1 = document.createElement("div");
                            comment_div1.className = "comment_div";

                            var comment_text1 = document.createElement("span");
                            comment_text1.className = "comment_text";
                            comment_text1.innerHTML = rep;

                            comment_div1.appendChild(comment_text1);
                            comment_texts1.appendChild(comment_div1);
                            comment_box.appendChild(comment_texts1);

                        }
                        var br13 = document.createElement("br");
                        comment_box.appendChild(br13);
                    }
                }

                first_comment_box.style.display = "none";
                comment_box.style.display = "inherit";
            }
        };
        httpReq.open("POST", link, false);
        httpReq.send();
    }
}

function viewMore() {

    var common_div = document.getElementById("common_div");
    var partition = document.createElement("div");
    common_div.appendChild(partition);

    var view_button = document.getElementById("view_more");
    if(view_button != null) {
        view_button.remove();
    }
    for(var i=post_index;i<post_index+3;i++) {

        var span4 = document.createElement("div");
        span4.className = "span4";
        span4.id = "div" + i;
        span4.style.position = "relative";
        partition.appendChild(span4);
        // add first span
        createPost(i,i);
    }
    post_index+=3;
    var button_div = document.createElement("div");
    button_div.style.clear = "both";
    common_div.appendChild(button_div);
    var view_more_button = document.createElement("button");
    view_more_button.innerHTML = "View more";
    view_more_button.id = "view_more";
    view_more_button.onclick = viewMore;
    button_div.appendChild(view_more_button);
}

function newPost() {
    var div0 = document.getElementById("div0");
    var new_post_box = document.createElement("div");
    new_post_box.id = "new_post_box";

    var br = document.createElement("br");
    new_post_box.appendChild(br);

    var form = document.createElement("form");
    form.action = "#";

    var textArea = document.createElement("textarea");
    textArea.cols = "45";
    textArea.rows = "1";
    textArea.id = "new_post";
    textArea.placeholder = "Share what's new...";
    form.appendChild(textArea);

    new_post_box.appendChild(form);

    var options = document.createElement("div");
    options.className = "options";
    new_post_box.appendChild(options);

    var option1 = document.createElement("div");
    option1.className = "option";

    var image1 = document.createElement("img");
    image1.className = "icon";
    image1.alt = "-";
    image1.src = "/images/Home%20Pic/1.png";

    var span1 = document.createElement("img");
    span1.className = "title";
    span1.id = "black_span";
    span1.innerHTML = "Text";

    option1.appendChild(image1);
    option1.appendChild(span1);
    options.appendChild(option1);

    var option2 = document.createElement("div");
    option2.className = "option";

    var image2 = document.createElement("img");
    image2.className = "icon";
    image2.alt = "-";
    image2.src = "/images/Home%20Pic/2.png";

    var span2 = document.createElement("img");
    span2.className = "title";
    span2.id = "black_span";
    span2.innerHTML = "Photos";

    option2.appendChild(image2);
    option2.appendChild(span2);
    options.appendChild(option2);

    var option3 = document.createElement("div");
    option3.className = "option";

    var image3 = document.createElement("img");
    image3.className = "icon";
    image3.alt = "-";
    image3.src = "/images/Home%20Pic/3.png";

    var span3 = document.createElement("img");
    span3.className = "title";
    span3.id = "black_span";
    span3.innerHTML = "Link";

    option3.appendChild(image3);
    option3.appendChild(span3);
    options.appendChild(option3);

    var option4 = document.createElement("div");
    option4.className = "option";

    var image4 = document.createElement("img");
    image4.className = "icon";
    image4.alt = "-";
    image4.src = "/images/Home%20Pic/4.png";

    var span4 = document.createElement("img");
    span4.className = "title";
    span4.id = "black_span";
    span4.innerHTML = "Video";

    option4.appendChild(image4);
    option4.appendChild(span4);
    options.appendChild(option4);

    var option5 = document.createElement("div");
    option5.className = "option";

    var image5 = document.createElement("img");
    image5.className = "icon";
    image5.alt = "-";
    image5.src = "/images/Home%20Pic/5.png";

    var span5 = document.createElement("img");
    span5.className = "title";
    span5.id = "black_span";
    span5.innerHTML = "Event";

    option5.appendChild(image5);
    option5.appendChild(span5);
    options.appendChild(option5);


    var option6 = document.createElement("div");
    option6.className = "option";

    var image6 = document.createElement("img");
    image6.className = "icon";
    image6.alt = "-";
    image6.src = "/images/Home%20Pic/6.png";

    var span6 = document.createElement("img");
    span6.className = "title";
    span6.id = "black_span";
    span6.innerHTML = "Poll";

    option6.appendChild(image6);
    option6.appendChild(span6);
    options.appendChild(option6);

    new_post_box.appendChild(options);
    div0.insertBefore(new_post_box, div0.firstChild);
}

$(document).ready(function(){
    $("#new_post").click(function(){
        $("#new_post_box").animate({left: '107%'}, 1200);
        $(".post_box").animate({top: '200px'}, 1500);
        $("#view_more").animate({top: '200px'}, 1500);
    });
});

function setTag(res_tag, post_text) {

    var a = document.createElement("a");
    a.href = "#";
    a.style.textDecoration = "none";
    var span = document.createElement("span");
    span.innerHTML = '#' + res_tag;
    span.onclick = function() {
        showPostsByTag(span.innerHTML);
    };
    span.style.marginLeft = '2%';
    span.style.fontWeight = "bold";
    span.style.color = "#2334CC";
    a.appendChild(span);
    post_text.appendChild(a);

}

function showPostsByTag(tagName) {


	var tag_posts;
	var http = new XMLHttpRequest();
	localStorage.clear();
	posts_object = new Array();
	hot_objects = new Array();
	likers = new Array();
	comments = new Array();

	var common_div = document.getElementById("common_div");
	http.onreadystatechange = function () {
		if (http.readyState == 4 && http.status == 200) {
			var xmlDoc = http.responseXML;
			tag_posts = xmlDoc.getElementsByTagName("posts")[0];
			alert(tag_posts.innerHTML);
			var items = tag_posts.getElementsByTagName("post");
			for(var i= 0; items.length > i; i++) {
				getPostXML(items[i].innerHTML);
			}

			localStorage.setItem('hots', JSON.stringify(hot_objects));
			localStorage.setItem('normal', JSON.stringify(posts_object));
			localStorage.setItem('likers', JSON.stringify(likers));
			localStorage.setItem('comments', JSON.stringify(comments));

			createPage();
		}
	};
	xmlhttp.open("POST", "http://plus.local/post/getposts/tag/" + tagName, true);
	xmlhttp.send();
}
