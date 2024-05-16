<?php

?>

<script>
    $(function() {


        var people = [];

        $.getJSON('people.json', function(data) {
            $.each(data.person, function(i, f) {
                var tblRow = "<tr>" + "<td>" + f.firstName + "</td>" +
                    "<td>" + f.lastName + "</td>" + "<td>" + f.job + "</td>" + "<td>" + f.roll + "</td>" + "</tr>"
                $(tblRow).appendTo("#userdata tbody");
            });

        });

    });
</script>


<?php

?>


<div class="wrapper">
    <div class="profile">
        <table id="userdata" border="2">
            <thead>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>City</th>
            </thead>
            <tbody>

            </tbody>
        </table>

    </div>
</div>

<?php

?>