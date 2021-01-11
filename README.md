## Installation
1. Copy manage-by-excel folder in qa-plugin to your project.
2. Create a new page and type the following in the HTML content

<form method="post" action="?qa=tag-edit"  enctype="multipart/form-data">
<input  type="file" name="excel_file" value="upload file"/>
<button name="doupload">Submit</button>
</form>

