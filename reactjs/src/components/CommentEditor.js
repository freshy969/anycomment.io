import React, {Fragment} from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import SendCommentFormBodyAvatar from './SendCommentFormBodyAvatar'
import {Editor} from 'react-draft-wysiwyg';
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';

class CommentEditor extends AnyCommentComponent {

    editorOptions = () => {
        return {
            options: ['inline', 'blockType', 'list', 'link', 'image'],
            inline: {
                inDropdown: false,
                options: ['bold', 'italic', 'underline', 'strikethrough'],
            },
            blockType: {
                inDropdown: false,
                options: ['Blockquote', 'Code'],
            },
            list: {
                inDropdown: false,
                options: ['unordered', 'ordered'],
            },
            link: {
                inDropdown: false,
                className: undefined,
                component: undefined,
                popupClassName: undefined,
                dropdownClassName: undefined,
                showOpenOptionOnHover: true,
                defaultTargetOption: '_self',
                options: ['link', 'unlink'],
            },
            image: {
                urlEnabled: true,
                uploadEnabled: false,
                alignmentEnabled: true,
                uploadCallback: undefined,
                previewImage: false,
                inputAccept: 'image/gif,image/jpeg,image/jpg,image/png,image/svg',
                alt: {present: false, mandatory: false},
                defaultSize: {
                    height: 'auto',
                    width: 'auto',
                },
            }
        };
    };

    render() {
        const {editorState} = this.props;

        return (
            <Fragment>
                <SendCommentFormBodyAvatar/>
                <Editor
                    editorState={editorState}
                    toolbar={this.editorOptions()}
                    toolbarClassName="toolbarClassName"
                    wrapperClassName="wrapperClassName"
                    editorClassName="editorClassName"
                    onEditorStateChange={this.props.handleEditorStateChange}
                />
            </Fragment>
        );
    }
}

export default CommentEditor;